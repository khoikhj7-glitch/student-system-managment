<style>
    .kpi-container { display: flex; gap: 20px; margin-bottom: 30px; flex-wrap: wrap; }
    .kpi-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--gray-200); flex: 1; min-width: 180px; text-align: center; box-shadow: var(--shadow-sm); }
    .kpi-card h3 { margin: 0; color: #6c757d; font-size: 14px; font-weight: 700; text-transform: uppercase; }
    .kpi-card p { font-size: 32px; font-weight: 800; margin: 10px 0 0; color: var(--primary); }
    .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; padding-bottom: 30px; }
    .chart-item { background: white; padding: 24px; border-radius: 12px; border: 1px solid var(--gray-200); display: flex; justify-content: center; align-items: center; min-height: 350px; box-shadow: var(--shadow-sm); }
</style>

<!-- Load Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-3 d-flex justify-between align-items-center">
    <h2>Hệ Thống Thống Kê & Báo Cáo SML</h2>
    <span class="badge bg-success">Real-time Data</span>
</div>

<div class="kpi-container">
    <div class="kpi-card"><h3>🏢 KHOA</h3><p id="khoa">0</p></div>
    <div class="kpi-card"><h3>📚 LỚP</h3><p id="lop">0</p></div>
    <div class="kpi-card"><h3>🧑‍🎓 SINH VIÊN</h3><p id="sv">0</p></div>
    <div class="kpi-card"><h3>📘 MÔN HỌC</h3><p id="mon">0</p></div>
    <div class="kpi-card"><h3>👨‍🏫 GIẢNG VIÊN</h3><p id="gv">0</p></div>
</div>

<div class="charts-grid">
    <div class="chart-item"><canvas id="genderChart"></canvas></div>
    <div class="chart-item"><canvas id="countryChart"></canvas></div>
    <div class="chart-item"><canvas id="scoreChart"></canvas></div>
    <div class="chart-item"><canvas id="chuyencanChart"></canvas></div>
    <div class="chart-item"><canvas id="trinhdoChart"></canvas></div>
</div>

<script>
// Fetch logic adapted for SML dynamic architecture
fetch('index.php?f=Dashboard&action=apiData')
.then(res => res.json())
.then(data => {
    // Đổ dữ liệu KPI
    document.getElementById("khoa").innerText = data.kpi.khoa;
    document.getElementById("lop").innerText = data.kpi.lop;
    document.getElementById("sv").innerText = data.kpi.sv;
    document.getElementById("mon").innerText = data.kpi.mon;
    document.getElementById("gv").innerText = data.kpi.gv;

    const colors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6'];

    // 1. Gender chart
    new Chart(document.getElementById("genderChart"), {
        type: 'pie',
        data: {
            labels: data.gender.map(i => i.gioi_tinh),
            datasets: [{ data: data.gender.map(i => i.total), backgroundColor: ['#3498db','#e74c3c'] }]
        },
        options: { plugins: { title: { display: true, text: 'Tỷ lệ Giới tính Sinh Viên' } } }
    });

    // 2. Country chart
    new Chart(document.getElementById("countryChart"), {
        type: 'doughnut',
        data: {
            labels: data.country.map(i => i.sv_nuoc_ngoai == 1 ? "Nước ngoài" : "Trong nước"),
            datasets: [{ data: data.country.map(i => i.total), backgroundColor: ['#2ecc71', '#f39c12'] }]
        },
        options: { plugins: { title: { display: true, text: 'Nguồn gốc Sinh Viên' } } }
    });

    // 3. Score chart
    new Chart(document.getElementById("scoreChart"), {
        type: 'bar',
        data: {
            labels: ["Dưới 5", "5 - 8", "Trên 8"],
            datasets: [{ label: 'Số lượng điểm tổng kết', data: [data.score.duoi5, data.score.tu5den8, data.score.tren8], backgroundColor: '#3498db' }]
        },
        options: { plugins: { title: { display: true, text: 'Phổ Điểm (Quy đổi Tỉ lệ %)' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // 4. Chuyên cần chart
    new Chart(document.getElementById("chuyencanChart"), {
        type: 'bar',
        data: {
            labels: ["1 buổi", "2 buổi", "3 buổi", "≥4 buổi"],
            datasets: [{ label: 'Số SV có vắng mặt theo môn', data: [data.chuyencan.buoi1, data.chuyencan.buoi2, data.chuyencan.buoi3, data.chuyencan.buoi4], backgroundColor: '#e67e22' }]
        },
        options: { plugins: { title: { display: true, text: 'Thống kê Mức độ vắng mặt' } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // 5. Trình độ giảng viên
    new Chart(document.getElementById("trinhdoChart"), {
        type: 'pie',
        data: { labels: data.trinhdo.map(i => i.trinh_do), datasets: [{ data: data.trinhdo.map(i => i.total), backgroundColor: colors }] },
        options: { plugins: { title: { display: true, text: 'Trình độ Giảng Viên' } } }
    });

})
.catch(err => console.error("Lỗi tải dữ liệu:", err));
</script>
