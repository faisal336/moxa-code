    <script src="assets/script.js"></script>
    <script src="assets/jquery-3.2.1.slim.min.js" ></script>
    <script src="assets/popper.min.js" ></script>
    <script src="assets/bootstrap.min.js" ></script>

    <script type="text/javascript">
        $('document').ready(function(){
            let sidebar = document.querySelector(".sidebar");
            sidebar.classList.toggle("open");
        });

       // $(function($) {
       //  let url = window.location.href;
       //    $('li a').each(function() {
       //      if (this.href === url) {
       //        $(this).closest('li').addClass('active');
            
       //      }
       //  });
       //  });
       $(document).ready(function(){
        $('.active img').addClass('black-show');
        $('.active .brown').removeClass('black-show');
        $('.active .brown').addClass('brown-hide');

       });

    </script>

