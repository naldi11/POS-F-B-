<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        .ticket {
            width: {{ $settings['paper_size'] === '80mm' ? '80mm' : ($settings['paper_size'] === 'a4' ? '210mm' : '58mm') }};
            padding: 5px;
            box-sizing: border-box;
            margin: 0 auto;
            background-color: #fff;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-lg { font-size: 14px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 12px; }
        .mt-4 { margin-top: 12px; }
        .pb-2 { padding-bottom: 4px; }
        .border-b { border-bottom: 1px dashed #000; }
        .border-t { border-top: 1px dashed #000; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-start { align-items: flex-start; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; padding: 2px 0; }
        .col-item { width: 50%; text-align: left; padding-right: 5px; }
        .col-qty { width: 15%; text-align: center; }
        .col-sub { width: 35%; text-align: right; }
        
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="text-center mb-4 border-b pb-2">
            <div class="font-bold text-lg">{{ $settings['store_name'] }}</div>
            <div>{{ $settings['address'] }}</div>
            @if($settings['phone']) <div>Telp: {{ $settings['phone'] }}</div> @endif
            @if($settings['email']) <div>Email: {{ $settings['email'] }}</div> @endif
            @if($settings['website']) <div>Web: {{ $settings['website'] }}</div> @endif
            @if($settings['social_media']) <div>{{ $settings['social_media'] }}</div> @endif
        </div>
        
        <div class="mb-4">
            <div class="flex justify-between items-start mb-2 border-b pb-2">
                <span style="white-space: nowrap; margin-right: 10px;">Waktu:</span>
                <span class="text-right">{{ \Carbon\Carbon::parse($order->created_at)->locale('id')->isoFormat('dddd, D MMM Y, HH:mm') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Pesanan:</span>
                <span>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Meja:</span>
                <span>{{ $order->table->table_number }}</span>
            </div>
            <div class="flex justify-between border-b pb-2 mb-2">
                <span>Pemesan:</span>
                <span>{{ $order->customer_name }}</span>
            </div>
        </div>

        <table class="mb-4">
            <thead>
                <tr>
                    <th class="col-item">Item</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-sub">Sub</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->orderDetails as $detail)
                <tr>
                    <td class="col-item">{{ $detail->menu->name }}</td>
                    <td class="col-qty">{{ $detail->quantity }}</td>
                    <td class="col-sub">{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t pt-2 mb-4">
            <div class="flex justify-between font-bold text-lg">
                <span>Total:</span>
                <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between mt-2">
                <span>Pembayaran:</span>
                <span>QRIS</span>
            </div>
            <div class="flex justify-between">
                <span>Status:</span>
                <span>LUNAS</span>
            </div>
        </div>

        <div class="text-center mt-4 border-t pt-2 border-b pb-2">
            <div>Terima Kasih</div>
            <div>Silakan Berikan Struk Ini Ke Dapur</div>
        </div>
        
        <div class="text-center mt-4 no-print">
            <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print Ulang</button>
            <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">Tutup</button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('download') === '1') {
            window.onload = function() {
                // Sembunyikan tombol saat screenshot
                const noPrint = document.querySelector('.no-print');
                noPrint.style.display = 'none';
                
                // Beri warna background putih eksplisit agar teks hitam terlihat jelas
                html2canvas(document.querySelector(".ticket"), {
                    backgroundColor: '#ffffff',
                    scale: 2 // Supaya resolusi gambarnya tajam (HD)
                }).then(canvas => {
                    let a = document.createElement('a');
                    a.href = canvas.toDataURL("image/png");
                    a.download = "Struk-Pesanan-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}.png";
                    a.click();
                    
                    // Munculkan lagi tombol
                    noPrint.style.display = 'block';
                    
                    // Tutup otomatis setelah unduh
                    setTimeout(() => window.close(), 1500);
                });
            };
        } else {
            window.onload = function() { window.print(); }
        }
    </script>
</body>
</html>
