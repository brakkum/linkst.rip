
$("#form_full_url").on("input", function() {
    let url = $(this).val();
    fetchUrlInfo(url);
});

fetchUrlInfo = url => {
    let save_button = document.getElementById("form_save");
    let error_div = document.getElementById("errors");
    fetch(`/api/valid_url?url=${url}`)
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.data.errors.length > 0) {
                error_div.innerHTML = data.data.errors[0];
            } else {
                error_div.innerHTML = "";
            }
        })
};

$("#form_slug").on("input", function() {
    let slug = $(this).val();
    if (slug) {
        $.ajax({
            url: `/api/slug/${slug}`
        })
        .done(function(response) {
            if (response.success) {
                $(this).data("valid", true);
            } else {
                $(this).data("valid", false);
            }
        });
    } else {
        $(this).data("valid", true);
    }
});

// $("#form_save").click(function(e) {
//     e.preventDefault();
//
//     let url = $("#form_full_url");
//
//     let slug = $("#form_slug");
// });
