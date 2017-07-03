$(document).ready(function () {

    tags = [];
    if (typeof jsVar['tagsAll'] != "undefined") {
        tags = jsVar['tagsAll'];
    }
    $('#tokenfield').tokenfield({
        autocomplete: {
            limit: 5,
            source: tags,
            delay: 100
        },
        showAutocompleteOnFocus: true,
        limit: 1
    })
});