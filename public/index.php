<?php
require_once("../common.php");
require_once("../sqlite.class.php");
checkCookie();
?>
<!DOCTYPE html>
<html lang="zh">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>临时公告板-用于发布临时消息的公告板</title>
   <!-- <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet"> -->
   <link href="style.css" rel="stylesheet">
</head>

<body>
   <div class="md:container mx-auto p-6 md:py-6">
      <div>
         <div class="mb-6 flex items-end">
            <h1 class="text-xl text-gray-900 font-bold ">临时公告板</h1>
            <p class="ml-2 text-gray-500 text-sm">用于发布临时消息的公告板</p>
         </div>

         <div class="md:w-full">
            <div class="ipt-text">
               <textarea id="text" class="resize-none border rounded-md w-full text-gray-600 focus:outline-none px-2 py-1" cols="50" rows="5"></textarea>
               <div class="flex justify-between items-center">
                  <p class="tips-text text-sm text-gray-400">*最多输入3000个字符</p>
                  <button class="border rounded-md shadow-md text-gray-500 text-base px-2 cursor-pointer w-16" onclick="submit()">提交</button>
               </div>
            </div>
         </div>

      </div>

      <div id="qrcode" class="mt-2"></div>

      <div class="mt-6">
         <p class="text-gray-600 text-base">*使用说明:</p>
         <p class="text-gray-500 text-sm">1. 提交文字后将得到一个唯一链接，例如 <?php echo buildBoardUrl('xxxx') ?></p>
         <p class="text-gray-500 text-sm">2. 使用时,可直接访问返回的链接或扫码二维码访问 </p>
         <p class="text-gray-500 text-sm">3. 您也可以自行使用唯一链接生成二维码以便分享访问 </p>
         <p class="text-gray-500 text-sm">4. 您发布的内容将保存7天,过期的公告将会被删除 </p>
         <p class="text-gray-500 text-sm">5. 历史记录为24h内最近10条，依靠cookie标记 </p>
      </div>

      <div class="my-6">
         <p class="text-gray-600 text-base">*历史记录:</p>
         <ul class="history mt-2"></ul>
      </div>
   </div>

   <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <script src="https://cdn.bootcdn.net/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
   <script type="text/javascript">
      var qrcode = null

      function submit() {
         const text = $('#text').val()
         if (text.length == 0) {
            alert('提交为空')
            return
         }
         const fd = new FormData()
         fd.append('message', text)
         $.ajax({
            url: '/submit.php',
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: res => {
               if (res.message == 'ok') {
                  $('#text').val('')
                  $('.tips-text').removeClass('text-gray-400')
                  $('.tips-text').addClass('text-green-400')
                  $('.tips-text').text('*提交成功，您的地址为 ' + res.data.url)
                  makeCode(res.data.url)
                  getHistory()
               }
            },
            error: err => {
               alert(err.responseJSON.error_message)
            }
         })
      }

      function makeCode(text) {
         if (qrcode == null) {
            qrcode = new QRCode("qrcode")
         }
         qrcode.clear()
         qrcode.makeCode(text)
      }

      function getHistory() {
         $.post('/history', function(res) {
            if (res.message == 'ok') {
               var html = ''
               res.data.forEach(function(item) {
                  html += `
                  <li class="flex items-center my-1">
                     <p class="text-sm text-gray-500 mr-4 hidden md:block">${item.created_at}</p>
                     <a href="${item.url}" class="text-gray-600 cursor-pointer mr-4">${item.url}</a>
                     <p class="text-gray-600 cursor-pointer w-80 overflow-hidden overflow-ellipsis whitespace-nowrap hidden md:block">${item.content}</p>
                  </li>
                  `
               })
               $('.history').html(html)
            }
         })
      }

      $(function() {
         getHistory()
      })
   </script>
</body>

</html>