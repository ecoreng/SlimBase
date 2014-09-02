        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?php echo $pluginsBaseUrl . $theme; ?>/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

        <script src="<?php echo $pluginsBaseUrl . $theme; ?>/js/plugins.js"></script>
        <script src="<?php echo $pluginsBaseUrl . $theme; ?>/js/main.js"></script>
        <?php echo $pageScripts; ?>

     <?php
     if (isset($jsBuffer) && $jsBuffer != '') {
     ?>
        <script>
            $(function() {     
            <?php
                echo $jsBuffer;
            ?>
            });
        </script>            
     <?php
     }
     ?>
    </body>
</html>
