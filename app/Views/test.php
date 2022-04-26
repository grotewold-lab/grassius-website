
<style>
.tooltip {
    position: relative;
    display: inline-block;
}
    
.tooltip image {
    cursor: pointer;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 170px;
    background-color: #555;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 8px 6px;
    position: absolute;
    z-index: 1;
    bottom: 150%;
    left: 50%;
    margin-left: -86px;
    opacity: 0;
    transition: opacity 0.3s;
    white-space:nowrap;
}

.tooltip .tooltiptext::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -5px;
    border-width: 5px;
    border-style: solid;
    border-color: #555 transparent transparent transparent;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}
</style>


<p>Click on the button to copy the text from the text field.</p>

<input type="text" value="Hello World" id="myInput" class="w3-input w3-border w3-left w3-margin-right w3-mobile" style="width:auto">

<div class="tooltip w3-mobile">
<button onclick="myFunction()" onmouseout="outFunc()" class="w3-button w3-border w3-light-grey w3-left w3-mobile">
  <span class="tooltiptext" id="myTooltip">Copy to clipboard</span>
  Copy text
  </button>
</div>

<script>
function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value)
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Copied: " + copyText.value;
}

function outFunc() {
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Copy to clipboard";
}
</script>

