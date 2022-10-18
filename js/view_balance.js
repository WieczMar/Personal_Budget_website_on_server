const mySelect = document.querySelector('#select-period');

mySelect.addEventListener('change', function () {
    if (mySelect.value === "Nonstandard"){
        const myModal = new bootstrap.Modal(document.querySelector('#exampleModal'),{});
            myModal.show();
    }
});
