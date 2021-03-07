  <script src="js/jquery-3.2.1.min.js"></script>
  <script src="js/tether.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/tagsinput.js"></script>
  <script src="js/bricks.js"></script>
  <script type="text/javascript">
    let log = console.log.bind('console');
    function debug(arg, title = "", style="") {
      if (title) { title += ': ' }
      if (typeof arg === 'object') {
        console.dir(arg);
      } else {
        console.log('%c' + title + '%c' + arg, 'color: #9999cc', '');
      }
    }

    function confirm_submit(action = '送信') {
      if (window.confirm(action + 'してよろしいですか？')) {
        return true;
      } else {
        return false;
      }
    }
//    $('#id').find('.class');
    $('.tagsinput').tagsinput({
      tagClass: '',
      maxChars: 20,
      maxTags: 20,
      trimValue: true,
      confirmKeys: [13, 32, 188],
    });

    window.onload = function disable_double_click(btn) {
      btn.disabled = true;
//      var forms = document.forms;
//      forms.forEach(function(_link, _winId){
//
//      });
    }

  </script>
</body>
</html>
