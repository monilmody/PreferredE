function updateSalecode(year) {
  
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("salecode").innerHTML = this.responseText;
      }
    };
    xmlhttp.open("GET","ajaxSaleCode.php?year="+year,true);
    xmlhttp.send();
  
}

function updateSalecode_tb(year) {
    // Assuming you have the breed value stored somewhere, you can retrieve it as needed
    var breed = document.getElementById("breed").value; // Adjust the ID as per your HTML structure
    
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("salecode").innerHTML = this.responseText;
        }
    };
    
    // Modify the URL to include both year and breed parameters
    xmlhttp.open("GET", "ajaxSaleCode.php?year=" + year + "&breed=" + breed, true);
    xmlhttp.send();
}

function showHint(str) {
	//alert(str);
    if (str.length == 0) { 
        document.getElementById("dam").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("dam").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "horseNameHint.php?q=" + str, true);
        xmlhttp.send();
    }
}