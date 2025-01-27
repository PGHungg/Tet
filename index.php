<?php
   $accountNo = isset($_POST['accountNo']) ? $_POST['accountNo'] : "";
   $accountName = isset($_POST['accountName']) ? $_POST['accountName'] : "";
   $acqId = isset($_POST['bank']) ? $_POST['bank'] : "";
   $amount = isset($_POST['amount']) ? $_POST['amount'] : "";
   $qrImage = "";
   $showModal = false;
   
   // Mảng thơ đòi nợ Tết ở đây !!!!
   $poems = [
    "Tết này nhìn lên bầu trời...\nTrên trời có triệu vì sao\nVì sao lớn nhất: Sao chưa trả tiền?",
    "Chạy theo xu hướng lì xì...\nChỉ mong xuân này mày lì xì nợ tao.",
    "Chắc bạn chưa quên...\nNợ tao mày cứ lần khân\nTết này không trả tao cân cả làng",
    "Tết này chẳng muốn ăn chơi...\nChỉ mong sớm được nợ đời trả xong!",
    "Xuân này chẳng thiếu bánh chưng...\nChỉ thiếu mày trả cho tao vài đồng!",
    "Năm nay đào mai nở khắp sân...\nNợ mày lãi suất giờ nhân mấy lần?",
    "Bánh chưng xanh, dưa hành muối...\nNhưng nợ mày chưa trả thì xuân tao tàn.",
    "Mứt ngon, bánh tét xanh rì...\nNhưng thiếu nợ mày sao mà ngon được?",
    "Mai đào nở khắp mọi nơi...\nTết này mà thiếu nợ, tao phơi mày luôn."
   ];
   
   // Lấy danh sách ngân hàng từ API
   $banksUrl = "https://api.vietqr.io/v2/banks";
   $banksResult = file_get_contents($banksUrl);
   $banks = [];
   if ($banksResult !== false) {
       $banksData = json_decode($banksResult, true);
       if (isset($banksData['data'])) {
           $banks = $banksData['data'];
       }
   }
   
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $amount = str_replace(',', '', $_POST['amount']);
       $poem = $poems[array_rand($poems)];
   
       $apiData = [
           "acqId" => $acqId,
           "accountNo" => $accountNo,
           "accountName" => $accountName,
           "amount" => (int)$amount,
           "addInfo" => "",
           "format" => "text",
           "template" => "compact"
       ];
   
       $url = "https://api.vietqr.io/v2/generate";
       $options = [
           'http' => [
               'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
               'method' => 'POST',
               'content' => json_encode($apiData),
           ],
       ];
       $context = stream_context_create($options);
       $result = file_get_contents($url, false, $context);
   
       if ($result !== false) {
           $response = json_decode($result, true);
           if (isset($response['data']['qrDataURL'])) {
               $qrImage = $response['data']['qrDataURL'];
               $showModal = true;
           }
       }
   }
   $poemLines = explode("\n", $poem);

   $firstLine = $poemLines[0];

   array_shift($poemLines); 

   $remainingLines = implode("\n", $poemLines);
   ?>
<!DOCTYPE html>
<html lang="vi">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Thơ Đòi Nợ Tết 2025</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
      <link href="style.css" rel="stylesheet">
      <style>
         .modal-content.Layout {
         background-color: rgb(119 18 17);
         background-image: url(bg.jpg);
         background-position: top;
         background-size: cover;
         }
         .decor {
         position: absolute;
         width: 15vw;
         height: 15vw;
         background-size: contain;
         background-repeat: no-repeat;
         }
         .Layout {
         display: flex;
         flex-direction: column;
         background-color: rgb(119 18 17);
         min-height: 100vh;
         background-image: url(bg.jpg);
         background-position: top;
         background-size: cover;
         padding: 20px;
         }
         .containers {
         background-color: rgba(255, 255, 255, 0.95);
         border-radius: 15px;
         padding: 30px;
         box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
         margin-top: 50px;
         margin-bottom: 50px;
         border: 2px solid #eccb7a;
         }
         .Title {
         position: relative;
         display: flex;
         align-items: center;
         justify-content: center;
         text-align: center;
         text-transform: uppercase;
         font-size: 4.5vw;
         filter: drop-shadow(rgba(0, 0, 0, .3333333333) 0 0 .5vw);
         color: #eccb7a;
         margin-bottom: 40px;
         }
         .Title > div:first-child {
         position: absolute;
         z-index: 0;
         -webkit-text-stroke: 0.22vw #eccb7a;
         color: transparent;
         }
         .form-label {
         color: #D4343F;
         font-weight: 600;
         font-size: 1.2rem;
         }
         .form-control, .form-select {
         border: 2px solid #eccb7a;
         padding: 12px;
         font-size: 1.1rem;
         background-color: rgba(255, 255, 255, 0.9);
         }
         .btn-primary {
         background-color: #D4343F;
         border-color: #eccb7a;
         padding: 15px;
         font-size: 1.3rem;
         text-transform: uppercase;
         font-weight: bold;
         letter-spacing: 1px;
         }
         .btn-primary:hover {
         background-color: #8B0000;
         border-color: #eccb7a;
         }
         .modal-content {
         background-color: #FFF5EE;
         border: 3px solid #eccb7a;
         }
         .modal-body {
         padding: 20px;
         text-align: center;
         }
         .poem-content {
         font-size: 6em; /* hoặc 96px */
         font-weight: bold;
         color: #ffecb4;
         line-height: 1.2;
         text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
         }
         @media (max-width: 768px) {
         .poem-content {
         font-size: 1.7em; /* hoặc 64px cho mobile */
         }
         }
         .poem-text {
         font-size: 1.5rem;
         color: #D4343F;
         text-align: center;
         white-space: pre-line;
         margin: 20px 0;
         padding: 20px;
         border-radius: 10px;
         border: 1px solid #eccb7a;
         background: linear-gradient(45deg, rgba(236,203,122,0.1), rgba(212,52,63,0.1));
         }
         .decor-foot {  bottom: -15px;     transform: translateY(-50%); }
         .qr-container {
         display: inline-block;
         padding: 20px;
         background-color: white;
         border-radius: 10px;
         margin: 20px 0;
         box-shadow: 0 0 15px rgba(236,203,122,0.3);
         border: 2px solid #eccb7a;
         width: 200px;
         }
         .qr-container img {
         width: 100%;
         height: auto;
         }
         .qr-info {
         margin-top: 20px;
         color: #D4343F;
         font-size: 1.2rem;
         }
         .main-title {
         color: #D4343F;
         font-size: 2.5rem;
         text-align: center;
         font-weight: bold;
         margin-bottom: 1.5rem;
         text-transform: uppercase;
         padding: 10px 20px;
         position: relative;
         }
         .developer-credit {
         text-align: center;
         color: #666;
         font-size: 0.9rem;
         margin-top: 2rem;
         padding-top: 1rem;
         border-top: 1px solid #eccb7a;
         }
         .developer-credit a {
         color: #D4343F;
         text-decoration: none;
         font-weight: 500;
         }
         .developer-credit a:hover {
         text-decoration: underline;
         }
      </style>
   </head>
   <body>
      <div class="Layout">
         <div class="containers">
            <h1 class="main-title">Thơ Đòi Nợ Vui</h1>
            <div class="developer-credit">Developed by <a href="https://www.facebook.com/harryvu205" target="_blank">Hoàng Phúc - FoxN</a></div>
            <form method="POST" class="mt-4">
               <div class="mb-3">
                  <label for="accountName" class="form-label">Tên muốn hiển thị (nhập gì cũng được)</label>
                  <input type="text" class="form-control" id="accountName" name="accountName" value="<?= htmlspecialchars($accountName) ?>" required>
               </div>
               <div class="mb-3">
                  <label for="accountNo" class="form-label">Số tài khoản</label>
                  <input type="text" class="form-control" id="accountNo" name="accountNo" value="<?= htmlspecialchars($accountNo) ?>" required>
               </div>
               <div class="mb-3">
                  <label for="bank" class="form-label">Ngân hàng</label>
                  <div class="position-relative">
                     <input type="text" class="form-control mb-2" id="bankSearch" placeholder="Tìm ngân hàng..." oninput="filterBanks(this.value)">
                     <select class="form-select" id="bank" name="bank" required size="5" style="height: 200px;">
                        <?php foreach ($banks as $bank): ?>
                        <option value="<?= htmlspecialchars($bank['bin']) ?>" 
                           <?= $bank['bin'] === $acqId ? 'selected' : '' ?>>
                           <?= htmlspecialchars($bank['shortName']) ?> - <?= htmlspecialchars($bank['name']) ?>
                        </option>
                        <?php endforeach; ?>
                     </select>
                     <div id="selectedBankInfo" class="mt-2 p-2 border rounded" style="display: none; background-color: rgba(236,203,122,0.1); border-color: #eccb7a;">
                        <strong style="color: #D4343F;">Ngân hàng đã chọn:</strong> <span id="bankDetails"></span>
                     </div>
                  </div>
               </div>
               <div class="mb-3">
                  <label for="amount" class="form-label">Số tiền</label>
                  <input type="text" class="form-control" id="amount" name="amount" value="<?= htmlspecialchars(number_format((int)$amount, 0, ',', ',')) ?>" value="0" required oninput="formatNumber(this)">
               </div>
               <button type="submit" class="btn btn-primary w-100">Tạo QR Code & Thơ Đòi Nợ</button>
            </form>
            <!-- Modal -->
            <div class="modal fade" id="qrModal" tabindex="-1">
               <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content Layout">
                     <div class="modal-body">
                        <?php if (!empty($qrImage)): ?>
                        <br><br><br><br><br><br><br>
                        <div class="flex justify-center">
                           <h1 class="w-full text-[#ffecb4] text-[6vw] md:text-[4.5vw] pt-[2.2vw] text-center">GỬI NGƯỜI TÔI YÊU QUÝ</h1>
                        </div>
                        <center>
                           <div class="line mb-[3%] relative">
                              <div class="birds top-0 -translate-y-full left-[-19%] -scale-x-100"></div>
                           </div>
                        </center>
                        <div class="Title">
                           <div><?= htmlspecialchars($firstLine) ?></div>
                           <div>
                              <?= htmlspecialchars($firstLine) ?>
                           </div>
                        </div>
                        <div class="poem-content text-center">
                           <div><?= nl2br(htmlspecialchars($remainingLines)) ?></div>
                        </div>
                        <br />
                        <center>
                           <div class="line mb-[3%] relative">
                              <div class="decor-full -translate-x-1/2 left-1/2 top-[-75%]" style="width: 95vw;"></div>
                           </div>
                        </center>
                        <div class="qr-info">
                           <div class="w-full text-[#ffecb4] text-[8vw] md:text-[1.9vw] pt-[1.2vw] text-center">
                              <div>Người nhận: <?= htmlspecialchars($accountName) ?></div>
                           </div>
                        </div>
                        <div class="relative">
                           <div class="qr-container">
                              <img src="<?= $qrImage ?>" alt="QR Code" class="img-fluid">
                           </div>
                           <div class="decor-foot pointer-events-none absolute -bottom-8"></div>
                        </div>
                        <?php else: ?>
                        <p class="text-danger">Không thể tạo QR Code.</p>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
      <script>
         function formatNumber(input) {
             let value = input.value.replace(/,/g, '');
             input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
         }
         
         document.addEventListener('DOMContentLoaded', function() {
             const form = document.querySelector('form');
             form.addEventListener('submit', function() {
                 localStorage.setItem('accountName', document.getElementById('accountName').value);
                 localStorage.setItem('accountNo', document.getElementById('accountNo').value);
                 localStorage.setItem('bank', document.getElementById('bank').value);
             });
         
             const savedAccountName = localStorage.getItem('accountName');
             const savedAccountNo = localStorage.getItem('accountNo');
             const savedBank = localStorage.getItem('bank');
         
             if (savedAccountName) document.getElementById('accountName').value = savedAccountName;
             if (savedAccountNo) document.getElementById('accountNo').value = savedAccountNo;
             if (savedBank) document.getElementById('bank').value = savedBank;
         });
         function filterBanks(searchText) {
         const select = document.getElementById('bank');
         const options = select.getElementsByTagName('option');
         
         for (let option of options) {
         const text = option.text.toLowerCase();
         const search = searchText.toLowerCase();
         option.style.display = text.includes(search) ? '' : 'none';
         }
         }
         
         // Auto-scroll to selected option when dropdown opens
         document.getElementById('bank').addEventListener('focus', function() {
         const selectedOption = this.options[this.selectedIndex];
         if (selectedOption) {
         selectedOption.scrollIntoView({ block: 'center' });
         }
         });
         document.getElementById('bank').addEventListener('change', function() {
         const selectedOption = this.options[this.selectedIndex];
         const infoDiv = document.getElementById('selectedBankInfo');
         const detailsSpan = document.getElementById('bankDetails');
         
         if (this.value) {
         detailsSpan.textContent = selectedOption.text;
         infoDiv.style.display = 'block';
         } else {
         infoDiv.style.display = 'none';
         }
         });
      </script>
      <?php if ($showModal): ?>
      <script>
         const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
         qrModal.show();
      </script>
      <?php endif; ?>
   </body>
</html>