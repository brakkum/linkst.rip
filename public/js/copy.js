
$("#link-out-button").on("click", function() {
    let link = document.getElementById("link-output");
    link.select();
    document.execCommand("copy");
});
