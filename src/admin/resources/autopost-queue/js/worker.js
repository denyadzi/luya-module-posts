zaa
  .service('autopostQueueWorker', ['$http', '$timeout', '$q', function($http, $timeout, $q) {

    var isRunning = false;

    function pollError(response) {
      console.log('Poll Error', response);
    }

    function finishSuccess(response) {
      console.log('Finish success', response);
    }

    function finishError(response) {
      console.log('Finish error', response);
    }

    function processJob(job) {
      var def = $q.defer();
      var jobId = job.id;
      var jobData = job.job_data;
      $http.post('/admin/api-posts-autopostqueuejob/'+jobId+'/reserve').then(function(response) {
        if (jobData.type == 'account_vk') {
          VK.Auth.login(function(response) {
            var params = {"v": "5.92"};
            if (jobData.postMessage) {
              params.message = jobData.message;
            }
            if (jobData.postLink) {
              params.attachments = jobData.link;
            }
            if (jobData.ownerId) {
              params.owner_id = jobData.ownerId;
            }
            VK.api('wall.post', params, function(data) {
              if (data.response) {
                $http.post('/admin/api-posts-autopostqueuejob/'+jobId+'/finish', { responseData: data.response })
                  .then(finishSuccess, finishError);
                def.resolve();
              } else {
                def.reject();
              }
            });
          }, 8192);
        } else if (jobData.type == 'facebook') {
          FB.login(function(response) {
            if (response.authResponse) {
              var params = {};
              // let user choose page token here, and set access_token param
              if (jobData.postMessage) {
                params.message = jobData.message;
              }
              if (jobData.postLink) {
                params.link = jobData.link;
              }
              FB.api('/me/feed', 'post', params, function(response) {
                if (! response || response.error) {
                  def.reject();
                } else {
                  $http.post('/admin/api-posts-autopostqueuejob/'+jobId+'/finish', { responseData: response })
                    .then(finishSuccess, finishError);
                  def.resolve();
                }
              });
            } else {
              console.log('User cancelled login or did not fully authorize.');
            }
          });          
        }
      });
      return def.promise;
    }
    
    function pollQueue() {
      $http.get('/admin/api-posts-autopostqueuejob/pending', {ignoreLoadingBar: true})
        .then(
          function(response) {
            var def = $q.defer();
            var chain = def.promise;
            for (var i in response.data) {
              var job = response.data[i];
              chain = chain.then(processJob.bind(null, job));
            }
            def.resolve();
            
            return chain;
          },
          pollError
        )
        .then(tick, function() { console.log('Chain rejected'); tick(); });
    }

    function tick() {
      $timeout(pollQueue, 3000);
    }
    
    this.run = function() {
      if (isRunning) return;

      isRunning = true;
      $http.get("/admin/api-posts-autopostqueuejob/worker-data").then(function(response) {
        if (! response.data.enabled) {
          return;
        }
        var vkAppId = response.data.vkAppId;
        var fbAppId = response.data.fbAppId;
        if (vkAppId) {
          window.vkAsyncInit = function() {
            VK.init({
              apiId: vkAppId,
            });
          };
        }
        if (fbAppId) {
          window.fbAsyncInit = function() {
            FB.init({
              appId            : fbAppId,
              autoLogAppEvents : true,
              xfbml            : true,
              version          : "v3.2"
            });
          };
        }

        $timeout(function() {
          if (vkAppId) {
            // remove when using own template >
            var vk = document.createElement("div");
            vk.id = "vk_api_transport";
            document.getElementsByTagName("body")[0].appendChild(vk);
            //<
            
            var el = document.createElement("script");
            el.type = "text/javascript";
            el.src = "https://vk.com/js/api/openapi.js?160";
            el.async = true;
            document.getElementById("vk_api_transport").appendChild(el);
          }
          if (fbAppId) {
            var el = document.createElement("script");
            el.type = "text/javascript";
            el.src = "https://connect.facebook.net/en_US/sdk.js";
            el.async = true;
            el.defer = true;
            document.getElementsByTagName("body")[0].appendChild(el);
          }
          tick();
        });
      });
    }
  }]);
