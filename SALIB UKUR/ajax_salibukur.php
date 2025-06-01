<script>
function loadAfrnData() {
    const selectElement = document.getElementById('no_afrn');
    const selectedId = selectElement.value;

    if (selectedId) {
        fetch(`insert_salibukur.php?get_data=1&id_afrn=${selectedId}&idSegel=`)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data) {
                    // Data utama
                    document.getElementById('tanggal').value = data.tgl_afrn || '';
                    document.getElementById('no_polisi').value = data.no_polisi || '';
                    document.getElementById('volume_bridger').value = data.volume || '';
                    document.getElementById('tgl_serti_akhir').value = data.tgl_serti_akhir || '';

                    // Tambahan: jarak komp
                    document.getElementById('jarak_komp1').value = data.jarak_komp1 || '186.5';
                    document.getElementById('jarak_komp2').value = data.jarak_komp2 || '194.3';
                    document.getElementById('jarak_komp3').value = data.jarak_komp3 || '207.4';
                    document.getElementById('jarak_komp4').value = data.jarak_komp4 || '0';

                    // Densitas cairan
                    document.getElementById('dencity_cair_komp1').value = data.dencity_cair_komp1 || '0.791';
                    document.getElementById('dencity_cair_komp2').value = data.dencity_cair_komp2 || '0.791';
                    document.getElementById('dencity_cair_komp3').value = data.dencity_cair_komp3 || '0.791';
                    document.getElementById('dencity_cair_komp4').value = data.dencity_cair_komp4 || '0.791';

                    // Suhu cairan
                    document.getElementById('temp_cair_komp_komp1').value = data.temp_cair_komp_komp1 || '30';
                    document.getElementById('temp_cair_komp_komp2').value = data.temp_cair_komp_komp2 || '30';
                    document.getElementById('temp_cair_komp_komp3').value = data.temp_cair_komp_komp3 || '30';
                    document.getElementById('temp_cair_komp_komp4').value = data.temp_cair_komp_komp4 || '30';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengambil data AFRN');
            });
    } else {
        // Clear all
        [
            'tanggal', 'no_polisi', 'volume_bridger', 'masa_berlaku_tangki',
            'jarak_komp1', 'jarak_komp2', 'jarak_komp3', 'jarak_komp4',
            'dencity_cair_komp1', 'dencity_cair_komp2', 'dencity_cair_komp3', 'dencity_cair_komp4',
            'temp_cair_komp_komp1', 'temp_cair_komp_komp2', 'temp_cair_komp_komp3', 'temp_cair_komp_komp4'
        ].forEach(key => {
            const el = document.getElementById(key);
            if (el) el.value = '';
        });
    }
}

// Handle form submit tanpa reload
document.getElementById('salibForm').addEventListener('submit', function(e) {
    e.preventDefault(); // cegah reload

    const form = e.target;
    const formData = new FormData(form);

    fetch('insert_salibukur.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            console.log('Hasil:', result);
            alert('Data berhasil disimpan!');
            form.reset(); // opsional: reset form setelah berhasil
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menyimpan data.');
        });
});
</script>