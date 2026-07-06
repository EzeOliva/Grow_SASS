<script>
  $(document).ready(function(){
    NXFeedbackLists().init();
    // Auto-open feedback create modal if redirected due to pending feedback
    if (new URLSearchParams(window.location.search).get('pending') === '1') {
      $.ajax({
        url: '/feedback/create',
        type: 'GET',
        dataType: 'json',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(response) {
          if (response.dom_html) {
            for (var i = 0; i < response.dom_html.length; i++) {
              var item = response.dom_html[i];
              if (item.action === 'replace') {
                $(item.selector).html(item.value);
              }
            }
          }
          $('#basicModal').modal({backdrop: 'static', keyboard: false});
          $('#basicModal').modal('show');
          if (typeof NXAddEditFeedback === 'function') {
            setTimeout(function(){ NXAddEditFeedback(); }, 200);
          }
        }
      });
    }
  });
</script>