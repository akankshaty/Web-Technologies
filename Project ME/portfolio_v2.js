var changeColor = function(id){
    console.log(id);
    var all = document.getElementsByClassName("nav-item");
    for(var i =0; i < all.length; i++)
        all[i].style.backgroundColor = "transparent";
    var elem = document.getElementById(id);
    elem.style.backgroundColor = "#17a2b8";
    elem.style.border = "#17a2b8";
    elem.style.color = "white";

};

function toggleDesc(id){
    var x = document.getElementById(id);
    if (x.style.display == "block") {
        x.style.display = "none";
    } else {
        x.style.display = "block";
    }    
}