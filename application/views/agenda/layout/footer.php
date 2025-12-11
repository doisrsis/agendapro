        </div>
    </div>

    <!-- Libs JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/pt-br.global.min.js'></script>

    <?php if ($this->session->flashdata('sucesso')): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: '<?= $this->session->flashdata('sucesso') ?>',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    <?php endif; ?>

    <?php if ($this->session->flashdata('erro')): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: '<?= $this->session->flashdata('erro') ?>',
            confirmButtonText: 'OK'
        });
    </script>
    <?php endif; ?>
</body>
</html>
