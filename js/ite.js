(function ($, Drupal) {
    Drupal.behaviors.ite_task_do = {
        attach: function (context, settings) {
            let percent = 1;
            let task_count = 0;
            let tasks = [];

            function getAllTasks(){
                $.ajax('/ite/all-images', {
                    success: function (data, status) {
                        for(let val of data)
                            tasks.push(val);

                        task_count = tasks.length;
                        doNextTask();
                    }
                });
            }

            function doNextTask() {
                let item = tasks.pop();
                $.ajax('/ite/image-to-entity/'+item.id, {
                    success: function (data, status) {
                        console.log(data);
                        calculatePercent();
                    }
                });
            }

            function calculatePercent() {
                let done_numbers = task_count - tasks.length;
                percent = ((done_numbers/task_count)*100).toFixed(0);
                changeMarkup();
                if(tasks.length > 0){
                    doNextTask();
                }else{
                    location.reload();
                }
            }

            function changeMarkup() {
                $('.progress__percentage').text(percent+'%');
                $('.progress__bar').css('width', percent+'%');
            }

            getAllTasks();

        }
    };
})(jQuery, Drupal);