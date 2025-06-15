<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>لوحة التحكم </title>
  <style>
    body { font-family: sans-serif; margin: 0; background: #f3f4f6; }
    nav { background: #2563eb; padding: .5rem 1rem; display: flex; justify-content: space-between; align-items: center; }
    nav ul { list-style: none; display: flex; gap: 1rem; margin: 0; padding: 0; }
    nav a, nav button {
      color: #fff; text-decoration: none; padding: .5rem 1rem; border-radius: .25rem;
      background: transparent; border: none; cursor: pointer; font-size: 1rem;
    }
    nav a.active, nav a:hover, nav button:hover { background: rgba(255,255,255,0.2); }
    .container { max-width: 900px; margin: 1rem auto; background: #fff; padding: 2rem;
                 border-radius: .5rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    .stats { display: flex; gap: 2rem; }
    .stat-box { flex: 1; background: #10b981; color: #fff; padding: 1rem;
                border-radius: .5rem; text-align: center; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th, td { border: 1px solid #ddd; padding: .5rem; text-align: left; }
    th { background: #f0f0f0; }
    .hidden { display: none; }
    .search { width: 100%; padding: .5rem; margin-bottom: 1rem;
              border: 1px solid #ccc; border-radius: .25rem; box-sizing: border-box; }
    button, select { padding: .4rem .8rem; border-radius: .25rem; border: 1px solid #ccc; cursor: pointer; }
    .btn-change { background: #10b981; color: #fff; border-color: #10b981; }
    .btn-download { background: #2563eb; color: #fff; border-color: #2563eb; }
    #message { color: #dc2626; margin-bottom: 1rem; }
  </style>
</head>
<body>

  <nav>
    <ul>
      <li><a href="#" id="nav-home" class="active">الصفحة الرئيسية</a></li>
      <li><a href="#" id="nav-projects">المشاريع</a></li>
      <li><a href="#" id="nav-users">المستخدمون</a></li>
    </ul>
    <button id="logoutBtn">تسجيل خروج</button>
  </nav>

  <div class="container">
    <div id="message"></div>

    <!-- الصفحة الرئيسية: إحصائيات -->
    <section id="statsSection">
      <h2>إحصائيات</h2>
      <div class="stats">
        <div class="stat-box">
          <h3>عدد المشاريع</h3>
          <p id="projectsCount">0</p>
        </div>
        <div class="stat-box">
          <h3>عدد المستخدمين</h3>
          <p id="usersCount">0</p>
        </div>
      </div>
    </section>

    <!-- جدول المشاريع -->
    <section id="projectsSection" class="hidden">
      <h2>المشاريع</h2>
      <table id="projectsTable">
        <thead>
          <tr>
            <th>#</th><th>العنوان</th><th>المقدم</th><th>تاريخ الإرسال</th>
            <th>الحالة</th><th>تحميل PDF</th><th>الوصف</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </section>

    <!-- جدول المستخدمين -->
    <section id="usersSection" class="hidden">
      <h2>المستخدمون</h2>
      <input type="text" id="userSearch" class="search" placeholder="ابحث بالرقم أو اسم المستخدم...">
      <table id="usersTable">
        <thead>
          <tr>
            <th>#</th><th>الاسم</th><th>اسم المستخدم</th><th>البريد الإلكتروني</th>
            <th>النوع</th><th>تاريخ التسجيل</th><th>تغيير كلمة المرور</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </section>
  </div>

  <script>
    // فحص التوكن عند فتح الصفحة
    (function() {
      const token = localStorage.getItem('api_token');
      if (!token) {
        window.location.href = '/api-login';
      }
    })();

    // تبديل الصفحات
    document.getElementById('nav-home').onclick = () => showSection('statsSection', 'nav-home');
    document.getElementById('nav-projects').onclick = () => showSection('projectsSection', 'nav-projects');
    document.getElementById('nav-users').onclick = () => showSection('usersSection', 'nav-users');
    function showSection(sectionId, navId) {
      ['statsSection','projectsSection','usersSection'].forEach(id =>
        document.getElementById(id).classList.toggle('hidden', id !== sectionId)
      );
      ['nav-home','nav-projects','nav-users'].forEach(id =>
        document.getElementById(id).classList.toggle('active', id === navId)
      );
    }

    // زر تسجيل الخروج
    document.getElementById('logoutBtn').onclick = async () => {
      const token = localStorage.getItem('api_token');
      if (token) {
        try {
          await fetch('/api/logout', {
            method: 'POST',
            headers: {
              'Accept': 'application/json',
              'Authorization': 'Bearer ' + token
            }
          });
        } catch (_) {}
        localStorage.removeItem('api_token');
      }
      window.location.href = '/api-login';
    };

    // تغيير كلمة المرور
    async function changePassword(userId) {
      const token = localStorage.getItem('api_token');
      if (!token) return alert('الرجاء تسجيل الدخول أولاً.');
      const pwd = prompt('أدخل كلمة المرور الجديدة:');
      if (!pwd) return;
      const conf = prompt('تأكيد كلمة المرور:');
      if (conf !== pwd) return alert('كلمتا المرور غير متطابقتين.');
      try {
        const res = await fetch(`/api/users/${userId}/password`, {
          method: 'POST',
          headers: {
            'Accept':'application/json',
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token
          },
          body: JSON.stringify({ password: pwd, password_confirmation: conf })
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message);
        alert(data.message);
      } catch (err) {
        alert(err.message);
      }
    }

    // تحديث حالة المشروع
    async function updateProjectStatus(projectId, newStatus) {
      const token = localStorage.getItem('api_token');
      if (!token) return alert('الرجاء تسجيل الدخول أولاً.');
      try {
        const res = await fetch(`/api/project-forms/${projectId}/status`, {
          method: 'PATCH',
          headers: {
            'Accept':'application/json',
            'Content-Type':'application/json',
            'Authorization':'Bearer '+token
          },
          body: JSON.stringify({ status: newStatus })
        });
        const data = await res.json();
        if (!res.ok) throw new Error(data.message);
        alert('تم تحديث الحالة إلى: '+newStatus);
      } catch (err) {
        alert(err.message);
      }
    }

    // تحميل PDF
    function downloadPdf(pdfPath) {
      const token = localStorage.getItem('api_token');
      if (!token) return alert('الرجاء تسجيل الدخول أولاً.');

      const url = `/storage/${pdfPath}`;
      const a = document.createElement('a');
      a.href = url;
      a.download = pdfPath.split('/').pop();  // استخدام اسم الملف من الـ URL
      a.click();
    }

    // جلب وعرض البيانات
    async function loadData() {
      const msg = document.getElementById('message');
      msg.textContent = '';
      const token = localStorage.getItem('api_token');
      if (!token) {
        msg.textContent = 'لم يتم العثور على التوكن. الرجاء تسجيل الدخول أولاً.';
        return;
      }
      try {
        const res = await fetch('/api/dashboard', {
          headers: {
            'Accept':'application/json',
            'Authorization':'Bearer '+token
          }
        });
        if (!res.ok) {
          const err = await res.json();
          msg.textContent = err.message;
          return;
        }
        const { projects, users } = await res.json();

        // إحصائيات
        document.getElementById('projectsCount').textContent = projects.length;
        document.getElementById('usersCount').textContent    = users.length;

        // جدول المشاريع
        const pBody = document.querySelector('#projectsTable tbody');
        pBody.innerHTML = '';
        projects.forEach(p => {
          const statuses = ['pending','accepted','rejected'];
          const opts = statuses.map(s =>
            `<option value="${s}" ${p.status===s?'selected':''}>${s}</option>`
          ).join('');
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${p.id}</td>
            <td>${p.title}</td>
            <td>${p.user?.username||'-'}</td>
            <td>${new Date(p.submitted_at).toLocaleString('en-US')}</td>
            <td><select onchange="updateProjectStatus(${p.id},this.value)">${opts}</select></td>
            <td><button class="btn-download" onclick="downloadPdf('${p.pdf_path}')">Download</button></td>
            <td>${p.description}</td>
          `;
          pBody.appendChild(tr);
        });

        // جدول المستخدمين
        const uBody = document.querySelector('#usersTable tbody');
        uBody.innerHTML = '';
        users.forEach(u => {
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${u.id}</td>
            <td>${u.name}</td>
            <td>${u.username}</td>
            <td>${u.email}</td>
            <td>${u.type}</td>
            <td>${new Date(u.created_at).toLocaleString('en-US')}</td>
            <td><button class="btn-change" onclick="changePassword(${u.id})">تغيير</button></td>
          `;
          uBody.appendChild(tr);
        });

        // بحث فوري على المستخدمين
        document.getElementById('userSearch').oninput = function() {
          const term = this.value.trim().toLowerCase();
          document.querySelectorAll('#usersTable tbody tr').forEach(row => {
            const idText = row.children[0].textContent.toLowerCase();
            const uname  = row.children[2].textContent.toLowerCase();
            row.style.display = (idText.includes(term)||uname.includes(term))?'':'none';
          });
        };

      } catch (e) {
        console.error(e);
        msg.textContent = 'فشل في الاتصال بالخادم.';
      }
    }

    window.addEventListener('DOMContentLoaded', () => {
      loadData();
      showSection('statsSection','nav-home');
    });
  </script>
</body>
</html>
