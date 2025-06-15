<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title> Login</title>
  <style>
    body { font-family: sans-serif; padding:2rem; background:#f3f4f6; }
    .card { background:#fff; max-width:400px; margin:auto; padding:1.5rem; border-radius:.5rem; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
    .field { margin-bottom:1rem; }
    label { display:block; margin-bottom:.5rem; }
    input { width:100%; padding:.5rem; border:1px solid #ccc; border-radius:.25rem; }
    button { background:#2563eb; color:#fff; padding:.5rem 1rem; border:none; border-radius:.25rem; cursor:pointer; }
    #message { margin-top:1rem; color:red; }
  </style>
</head>
<body>
  <div class="card">
    <h2 class="mb-4 text-xl font-bold">تسجيل الدخول </h2>
    <form id="loginForm">
      <div class="field">
        <label for="username">اسم المستخدم</label>
        <input id="username" type="text" required>
      </div>
      <div class="field">
        <label for="password">كلمة المرور</label>
        <input id="password" type="password" required>
      </div>
      <button type="submit">دخول</button>
    </form>
    <div id="message"></div>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const msg = document.getElementById('message');
      msg.textContent = '';
      
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value;
      const loginUrl = "{{ url('api/login') }}";
      const userUrl  = "{{ url('api/user') }}";

      try {
        // 1. تسجيل الدخول وإصدار التوكن
        const loginRes = await fetch(loginUrl, {
          method: 'POST',
          headers: { 'Content-Type':'application/json' },
          body: JSON.stringify({ username, password })
        });
        const loginData = await loginRes.json();
        if (!loginRes.ok) {
          msg.textContent = loginData.message || 'خطأ في بيانات الاعتماد';
          return;
        }

        // خزّن التوكن مؤقتًا
        localStorage.setItem('api_token', loginData.access_token);

        // 2. جلب بيانات المستخدم الحالي
        const userRes = await fetch(userUrl, {
          headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer ' + loginData.access_token
          }
        });
        const userData = await userRes.json();
        if (!userRes.ok) {
          throw new Error(userData.message || 'خطأ في جلب بيانات المستخدم');
        }

        // 3. تحقق من نوع المستخدم
        if (userData.type !== 'admin') {
          // لمسح التوكن ومنع الوصول
          localStorage.removeItem('api_token');
          msg.textContent = 'تلحس طيزي شو فتح / الرجاء ادخال يوزر ادمن ';
          return;
        }

        // 4. إذا كان admin فعلاً، وجّه إلى الداشبورد
        window.location.href = "{{ url('dashboard-page') }}";

      } catch (err) {
        console.error(err);
        msg.textContent = 'فشل في الاتصال بالخادم. انظر الكونسول.';
      }
    });
  </script>
</body>
</html>
