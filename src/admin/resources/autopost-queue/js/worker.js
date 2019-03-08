zaa
  .service('autopostQueueWorker', ['$http', '$timeout', '$q', function($http, $timeout, $q) {

    $http.get('/admin/api-posts-socialappsconfig/get').then(function(response) {
      var vkAppId = response.data.vkAppId;
      if (vkAppId) {
        window.vkAsyncInit = function() {
          VK.init({
            apiId: vkAppId,
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
      });
    });

    
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
        .then(tick, function() { console.log('Chain rejected') });
    }

    function tick() {
      $timeout(pollQueue, 10000);
    }
    
    this.run = function() {
      tick();
    }
  }])
  .run(['autopostQueueWorker', function(autopostQueueWorker) {
    autopostQueueWorker.run();
  }]);
