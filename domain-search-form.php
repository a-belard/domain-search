<form class="flex flex-column" id="domain-search-form" action="#">
  <div class="domain-search-container">
    <div class="input-container">
      <input type="text" name="domain" id="domainIput" placeholder="Find a .RW domain" required style="background-color: rgba(255, 255, 255, 0) !important; outline: none !important; color: rgba(0,0,0,0.8) !important;">
      <select name="tld" id="tld" required class="h-50" style="color: rgba(0,0,0,0.8) !important; border-radius: 5px ;
      ">
        <option value=".rw" selected>.rw</option>
        <option value=".co.rw">.co.rw</option>
        <option value=".net.rw">.net.rw</option>
        <option value=".org.rw">.org.rw</option>
        <option value=".coop.rw">.coop.rw</option>
        <option value=".ac.rw">.ac.rw</option>
      </select>
    </div>
    <button type="submit" style="background-color: white !important; color: #2C8CCE !important">
      Search
    </button>
  </div>
  <div id="results" style="color: white; text-align: center;
   font-weight: bold !important;
  "></div>

</form>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    $("#domain-search-form").submit(function(e) {
      e.preventDefault();
      const domain = document.getElementById('domainIput').value;
      const tld = document.getElementById('tld').value;

      $.post('<?php echo plugin_dir_url(__FILE__) . 'api/lookup.php' ?>', {
        domain,
        tld
      }, function(data) {
        $('#results').html(data);
      });
    });
  });
</script>