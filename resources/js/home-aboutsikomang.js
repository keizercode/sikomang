window.openModal = function (id) {
    const modal = document.getElementById(id);

    if (!modal) {
        console.warn(`Modal dengan id "${id}" tidak ditemukan`);
        return;
    }

    modal.classList.remove("hidden");
};

window.closeModal = function (id) {
    const modal = document.getElementById(id);

    if (!modal) return;
    modal.classList.add("hidden");
};
