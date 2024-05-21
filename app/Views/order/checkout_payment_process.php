<?= $this->extend('layouts/main_layout') ?>
<?= $this->section('content') ?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-10 card p-4">

            <h4 class="text-secondary text-center my-5">Processo de pagamento simulado</h4>
            <h5 class="text-center mb-5">Introduza o seu cartão de crédito na máquina e insira o PIN de 4 dígitos</h5>

            <div class="mb-5">
                <?= form_open(site_url('/order/checkout_payment_confirm')) ?>
                <input type="hidden" name="pin_value" value="<?= Encrypt($pin_number) ?>">
                <div class="row justify-content-center mb-3">
                    <h4 class="text-center mb-3">PIN do seu cartão</h4>
                    <div class="col-lg-4 col-8">
                        <input type="text" name="pin_number" id="pin_number" class="form-control text-center" placeholder="0000" minlength="4" maxlength="4">
                    </div>
                </div>

                <?php if (!empty($error)) : ?>
                    <h5 class="text-center text-danger"><?= $error ?></h5>
                <?php endif; ?>

                <div class="text-center my-4">
                    <button type="submit" class="cig-primary">
                        <h4><i class="fas fa-check me-3"></i>Confirmar PIN</h4>
                    </button>
                </div>

                <div class="text-center">
                    <a href="<?= site_url('/order/checkout_payment') ?>" class="cig-primary"><i class="fas fa-times me-3"></i>Cancelar</a>
                </div>
                <?= form_close() ?>
            </div>

            <div class="text-center text-secondary">
                <small><?= $pin_number ?></small>
            </div>
        </div>
    </div>
</div>

<script>
    // only allow numbers in the pin number input
    document.getElementById('pin_number').addEventListener('input', function(event) {
        this.value = this.value.replace(/[^0-9]/, '')
    });

    // prevent from submission if the pin number is 4 digits
    document.querySelector('form').addEventListener('submit', () => {
        if (document.getElementById('pin_number').value.length != 4) {
            event.preventDefault();
        }
    });
</script>

<?= $this->endSection() ?>