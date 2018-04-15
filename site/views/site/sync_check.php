<style type="text/css">
* {
  text-align: left;
}

td {
  min-width: 200px;
}

h2 {
  font-size: 18px;
}
</style>
<h2>Sync from block 0</h2>
<table border="1">
  <th>
    Node
  </th>
  <th>
    Height
  </th>
  <th>
    alt_blocks_count
  </th>
  <th>
    status
  </th>
  <th>
    Updated
  </th>
  <?php foreach ($scratch as $node => $data): ?>
    <tr>
      <td>
        <?php echo $node?>
      </td>
      <td id="<?php echo str_replace(".", "-", $node) ?>-height">
        <?php echo (isset($data['height']) ? $data['height'] : 0)?>
      </td>
      <td id="<?php echo str_replace(".", "-", $node) ?>-alts">
        <?php echo (isset($data['alts']) ? $data['alts'] : 0)?>
      </td>
      <td id="<?php echo str_replace(".", "-", $node) ?>-status">
        <?php echo (isset($data['status']) ? $data['status'] : $data['error'])?>
      </td>
      <td>
        <span id="<?php echo str_replace(".", "-", $node) ?>-time"></span>
        <script type="text/javascript">
          $(document).ready(function() {
            window.setInterval(function(){
              $("#127-0-0-1-time").html("Checking...");
              $.get("/site/update?ip=<?php echo $node ?>", function( data ) {
                //$( ".result" ).html( data );
                var res = $.parseJSON(data);
                var currentdate = new Date();
                var localtime =  currentdate.getHours() + ":"
                            + currentdate.getMinutes() + ":"
                            + currentdate.getSeconds();
                $("#<?php echo str_replace(".", "-", $node) ?>-height").html(res.height);
                $("#<?php echo str_replace(".", "-", $node) ?>-alts").html(res.alts);
                $("#<?php echo str_replace(".", "-", $node) ?>-status").html(res.status);
                $("#<?php echo str_replace(".", "-", $node) ?>-time").html(localtime);
                //alert( "Load was performed." );
              });

              //document.write(localtime);
            }, 5000);
          });



        </script>
      </td>
    </tr>
  <?php endforeach ?>
</table>

<h2>Sync from block 104948</h2>
<table border="1">
  <th>
    Node
  </th>
  <th>
    Height
  </th>
  <th>
    alt_blocks_count
  </th>
  <th>
    status
  </th>
  <?php foreach ($resume as $node => $data): ?>
    <tr>
      <td>
        <?php echo $node?>
      </td>
      <td id="<?php echo str_replace(".", "-", $node) ?>-height">
        <?php echo (isset($data['height']) ? $data['height'] : 0)?>
      </td>
      <td id="<?php echo str_replace(".", "-", $node) ?>-alts">
        <?php echo (isset($data['alts']) ? $data['alts'] : 0)?>
      </td>
      <td id="<?php echo str_replace(".", "-", $node) ?>-status">
        <?php echo (isset($data['status']) ? $data['status'] : $data['error'])?>
      </td>
      <td>
        <span id="<?php echo str_replace(".", "-", $node) ?>-time"></span>
        <script type="text/javascript">
          $(document).ready(function() {
            window.setInterval(function(){
              $("#127-0-0-1-time").html("Checking...");
              $.get("/site/update?ip=<?php echo $node ?>", function( data ) {
                //$( ".result" ).html( data );
                var res = $.parseJSON(data);
                var currentdate = new Date();
                var localtime =  currentdate.getHours() + ":"
                            + currentdate.getMinutes() + ":"
                            + currentdate.getSeconds();
                $("#<?php echo str_replace(".", "-", $node) ?>-height").html(res.height);
                $("#<?php echo str_replace(".", "-", $node) ?>-alts").html(res.alts);
                $("#<?php echo str_replace(".", "-", $node) ?>-status").html(res.status);
                $("#<?php echo str_replace(".", "-", $node) ?>-time").html(localtime);
                //alert( "Load was performed." );
              });

              //document.write(localtime);
            }, 5000);
          });



        </script>
      </td>
    </tr>
  <?php endforeach ?>
</table>
