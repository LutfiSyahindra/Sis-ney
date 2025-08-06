<div class="modal fade" id="BudgetPlanModal" tabindex="-1" aria-labelledby="BudgetPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="BudgetPlanModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                @stack("modalBudgetPlanContent")
            </div>
        </div>
    </div>
</div>

{{-- Modal Deposit --}}
<div class="modal fade" id="DepositModal" tabindex="-1" aria-labelledby="DepositModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DepositModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                @stack("modalDepositContent")
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="DetailModal" tabindex="-1" aria-labelledby="DetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DetailModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                @stack("modalDetailContent")
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Edit --}}
<div class="modal fade" id="DetailEditModal" tabindex="-1" aria-labelledby="DetailEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DetailEditModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="btn-close"></button>
            </div>
            <div class="modal-body">
                @stack("modalDetailEditContent")
            </div>
        </div>
    </div>
</div>
