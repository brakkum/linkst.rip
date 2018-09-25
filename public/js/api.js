
$("#form_full_url").on("input", function() {
    let url = $(this).val();
    fetchUrlInfo(url);
});

fetchUrlInfo = url => {
    let url_input = document.getElementById("form_full_url");
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

            if (data.success) {
                if (!url_input.classList.contains("data-valid")) {
                    url_input.classList.add("data-valid");
                }
            } else {
                if (url_input.classList.contains("data-valid")) {
                    url_input.classList.remove("data-valid");
                }
            }
        })
};

fetchSlugInfo = slug => {
    let slug_input = document.getElementById("form_slug");
    let error_div = document.getElementById("errors");

    if (slug) {
        fetch(`/api/slug/${slug}`)
            .then(response => response.json())
            .then(data => {
                if (data.data.errors.length > 0) {
                    error_div.innerHTML = data.data.errors[0];
                } else {
                    error_div.innerHTML = "";
                }

                if (data.success) {
                    if (!slug_input.classList.contains("data-valid")) {
                        slug_input.classList.add("data-valid");
                    }
                } else {
                    if (slug_input.classList.contains("data-valid")) {
                        slug_input.classList.remove("data-valid");
                    }
                }
            });
    } else {
        error_div.innerHTML = "";
        if (!slug_input.classList.contains("data-valid")) {
            slug_input.classList.add("data-valid");
        }
    }
};

$("#form_slug").on("input", function() {
    let slug = $(this).val();
    fetchSlugInfo(slug);
});

let save_button = document.getElementById("form_save");


// $("#form_save").click(function(e) {
//     e.preventDefault();
//
//     let url = $("#form_full_url");
//
//     let slug = $("#form_slug");
// });
