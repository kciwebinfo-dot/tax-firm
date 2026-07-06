    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="<?= e(asset('js/ajax.js')) ?>"></script>
<script src="<?= e(asset('js/common.js')) ?>"></script>
<?php if ($flash = flash_get()): ?>
<script>TaxPortal.toast('<?= e($flash['type']) ?>', '<?= e($flash['message']) ?>');</script>
<?php endif; ?>
</body>
</html>
