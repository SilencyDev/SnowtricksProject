function deleteSnowtrick(event) {
    event.preventDefault();
    const that = this;
    const url = this.action;
    let token = this.querySelector('input[name="_token"]');
    if (confirm('confirm delete action ?')) {
        axios.post(url, {
            token: token.value
        }).then( function (response) {
            if (response.status === 204) {
                that.parentNode.parentNode.remove();

            } else {

            }
        });

    };
}

document.querySelectorAll('form.snowtrick').forEach( function(form) {
    form.addEventListener('submit', deleteSnowtrick);
})