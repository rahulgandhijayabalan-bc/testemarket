<?php
/* =-=-=-= Copyright © 2018 eMarket =-=-=-=  
  |    GNU GENERAL PUBLIC LICENSE v.3.0    |
  |  https://github.com/musicman3/eMarket  |
  =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-= */
?>
<link href="/ext/tail/tail.select-primary.css" rel="stylesheet" type="text/css">
<script src="/ext/tail/tail.select.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/js_handler/admin/pages/zones/listing/main.js"></script>
<script type="text/javascript">
    var lang = <?php echo json_encode(lang()) ?>;
    new ZonesListing(lang);
</script>
