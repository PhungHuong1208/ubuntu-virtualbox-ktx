<?php
$type = $type ?? 'electricity';
$isElectricity = $type === 'electricity';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>css/form.css?v=<?= time() ?>">
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <div>
                <a href="<?= BASE_URL ?>utility?tab=<?= $type ?>" class="back-link">← Quay lại Danh Sách</a>
            </div>
            <h1><?= $title ?></h1>
        </div>

        <form method="post" action="<?= BASE_URL ?>utility/store" id="utilityForm">
            <input type="hidden" name="type" value="<?= $type ?>">
            
            <div class="form-row-3">
                <div class="form-group required">
                    <label>Mã HĐ <?= $isElectricity ? 'Điện' : 'Nước' ?></label>
                    <input type="text" name="<?= $isElectricity ? 'matd' : 'matn' ?>" required value="<?= htmlspecialchars($invoiceCode ?? '') ?>" readonly class="readonly-field">
                    <div class="field-hint">Mã được tạo tự động</div>
                </div>
                <div class="form-group required">
                    <label>Mã Phòng</label>
                    <select name="maphong" id="maphong" required>
                        <option value="">-- Chọn phòng --</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= htmlspecialchars($room['maphong']) ?>"><?= htmlspecialchars($room['maphong']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Ngày tính</label>
                    <input type="date" name="ngay" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-row-3">
                <div class="form-group required">
                    <label>Số <?= $isElectricity ? 'điện' : 'nước' ?> sử dụng (<?= $isElectricity ? 'kWh' : 'm³' ?>)</label>
                    <input type="number" id="usage" min="0" value="0" required step="0.1">
                </div>
                <div class="form-group required">
                    <label>Trạng Thái</label>
                    <input type="text" value="Chưa thanh toán" readonly class="readonly-field">
                    <input type="hidden" id="trangthai" name="trangthai" value="Chưa thanh toán">
                </div>
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-primary" onclick="calculate()" style="width: 100%;">🧮 Tính toán</button>
                </div>
            </div>

            <div id="alertBox"></div>

            <div class="result-box">
                <label style="font-weight: 600; color: #2c3e50; display: block; margin-bottom: 15px;">Kết quả chi tiết:</label>
                <div id="resultDetails" style="min-height: 100px;"></div>
                <div class="total-amount" id="totalAmount" style="display: none;">
                    Tổng cộng: <span id="totalValue">0</span> VNĐ
                </div>
            </div>

            <input type="hidden" name="<?= $isElectricity ? 'giadien' : 'gianuoc' ?>" id="amountInput">

            <div class="form-actions">
                <button type="button" class="btn btn-success" onclick="saveInvoice()">💾 Lưu Hóa Đơn</button>
                <a href="<?= BASE_URL ?>utility?tab=<?= $type ?>" class="btn btn-secondary">✕ Hủy</a>
            </div>
        </form>
    </div>

    <script>
        const isElectricity = <?= json_encode($isElectricity) ?>;
        function calculate() {
            const usage = parseFloat(document.getElementById('usage').value) || 0;
            const alertBox = document.getElementById('alertBox');
            alertBox.innerHTML = '';
            if (usage < 0) {
                alertBox.innerHTML = '<div class="alert alert-error">⚠️ Số lượng không được âm!</div>';
                return;
            }
            let resultHTML = '';
            let total = 0;
            if (isElectricity) {
                const tiers = [
                    { limit: 50, price: 1806 }, { limit: 50, price: 1866 },
                    { limit: 100, price: 2167 }, { limit: 100, price: 2729 },
                    { limit: 100, price: 3050 }, { limit: Infinity, price: 3151 }
                ];
                let remaining = usage;
                tiers.forEach((tier, index) => {
                    if (remaining <= 0) return;
                    const used = Math.min(remaining, tier.limit);
                    const cost = used * tier.price;
                    total += cost;
                    resultHTML += `<div class="result-item"><span>Bậc ${index + 1}: ${used.toFixed(1)} kWh × ${tier.price.toLocaleString()} VND</span><strong>${cost.toLocaleString()} VND</strong></div>`;
                    remaining -= used;
                });
            } else {
                const pricePerM3 = 4000;
                total = usage * pricePerM3;
                resultHTML = `<div class="result-item"><span>${usage.toFixed(1)} m³ × ${pricePerM3.toLocaleString()} VND/m³</span><strong>${total.toLocaleString()} VND</strong></div>`;
            }
            document.getElementById('resultDetails').innerHTML = resultHTML;
            document.getElementById('totalValue').textContent = total.toLocaleString();
            document.getElementById('totalAmount').style.display = 'block';
            document.getElementById('amountInput').value = total;
        }

        function saveInvoice() {
            const maphong = document.getElementById('maphong').value;
            const amount = document.getElementById('amountInput').value;
            if (!maphong) { alert('Vui lòng chọn mã phòng!'); return; }
            if (!amount || parseInt(amount) === 0) { alert('Vui lòng tính toán trước khi lưu!'); return; }
            const confirmedAmount = parseInt(amount).toLocaleString();
            if (confirm(`Số tiền là ${confirmedAmount} VNĐ. Bạn có chắc muốn lưu vào hệ thống?`)) {
                document.getElementById('utilityForm').submit();
            }
        }

        document.getElementById('usage').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); calculate(); }
        });
    </script>
</body>
</html>