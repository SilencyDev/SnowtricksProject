$("#comments").on("submit", "form.comment", function(event) {
    event.preventDefault();
    console.log("test");
    const that = this;
    const url = this.action;
    let token = this.querySelector('input[name="_token"]');
    if (confirm('confirm delete action ?')) {
        axios.post(url, {
            token: token.value
        }).then( function (response) {
            console.log(response);
            if (response.status === 201) {
                that.parentNode.parentNode.parentNode.parentNode.parentNode.remove();
            } else {
                alert("an error occured");
            }
        });
    };
});