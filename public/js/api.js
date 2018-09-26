
$("#form_url").on("input paste", function() {
    let url = $(this).val();
    doDelayedUrlSearch(url);
});

$("#form_slug").on("input paste", function() {
    let slug = $(this).val();
    doDelayedSlugSearch(slug);
});

let urlTimeout = null;

function doDelayedUrlSearch(url) {
    if (urlTimeout) {
        clearTimeout(urlTimeout);
    }
    urlTimeout = setTimeout(function() {
        fetchUrlInfo(url);
    }, 500);
}

let slugTimeout = null;

function doDelayedSlugSearch(slug) {
    if (slugTimeout) {
        clearTimeout(slugTimeout);
    }
    slugTimeout = setTimeout(function() {
        fetchSlugInfo(slug);
    }, 500);
}

fetchUrlInfo = url => {
    let url_input = document.getElementById("form_url");
    let error_div = document.getElementById("errors");

    if (url) {
        fetch(`/api/valid_url?url=${url}`)
            .then(response => response.json())
            .then(data => {
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
            .then(() => {
                updateSaveButton();
            });
    } else {
        error_div.innerHTML = "";
        if (url_input.classList.contains("data-valid")) {
            url_input.classList.remove("data-valid");
        }
        updateSaveButton();
    }
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

                if (data.success && data.data.slugAvailable) {
                    if (!slug_input.classList.contains("data-valid")) {
                        slug_input.classList.add("data-valid");
                    }
                } else {
                    if (slug_input.classList.contains("data-valid")) {
                        slug_input.classList.remove("data-valid");
                    }
                }
            })
            .then(() => {
                updateSaveButton();
            });
    } else {
        error_div.innerHTML = "";
        if (!slug_input.classList.contains("data-valid")) {
            slug_input.classList.add("data-valid");
        }
        updateSaveButton();
    }
};

validateBoth = () => {
    let url = document.getElementById("form_url");
    let slug = document.getElementById("form_slug");
    let url_val = url.value;
    let slug_val = slug.value;

    fetchUrlInfo(url_val);
    fetchSlugInfo(slug_val);
};

updateSaveButton = () => {
    let url = document.getElementById("form_url");
    let slug = document.getElementById("form_slug");
    let button = document.getElementById("form_save");

    if (url.classList.contains("data-valid") &&
            slug.classList.contains("data-valid")) {
        button.disabled = false;
        button.classList.add("data-valid");
    } else {
        button.disabled = true;
        button.classList.remove("data-valid");
    }
};

$("#form_save").on("click", function(e) {
    e.preventDefault();
    validateBoth();
    updateSaveButton();
    let submit_button = document.getElementById("form_save");
    let form = document.getElementById("link_form");

    if (submit_button.disabled === false) {
        form.submit();
    }
});
