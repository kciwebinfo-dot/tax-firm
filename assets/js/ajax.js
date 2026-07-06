window.TaxAjax = {
    post(url, data) {
        return $.ajax({
            url,
            method: 'POST',
            data,
            headers: { 'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content }
        });
    }
};
