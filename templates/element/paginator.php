<!-- templates/element/paginator.php -->
<div class="paginator">
  <ul class="pagination">
    <?= $this->Paginator->first('<<') ?>
    <?= $this->Paginator->prev('<') ?>
    <?= $this->Paginator->numbers() ?>
    <?= $this->Paginator->next('>') ?>
    <?= $this->Paginator->last('>>') ?>
  </ul>
</div>