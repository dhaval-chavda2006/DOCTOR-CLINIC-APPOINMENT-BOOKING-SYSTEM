
<?php
session_start();

// Check if user is logged in AND if they are actually a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    // If not, send them back to the login page
    header("Location: login_doctor.html"); 
    exit();
}
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Doctor Dashboard | Clinic System</title>
       <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
 
    <style>
      /* ── Palette Variables ── */
      :root {
        --primary-100: #0D6E6E;
        --primary-200: #4a9d9c;
        --primary-300: #afffff;
        --accent-100:  #FF3D3D;
        --accent-200:  #ffe0c8;
        --text-100:    #FFFFFF;
        --text-200:    #e0e0e0;
        --bg-100:      #0D1F2D;
        --bg-200:      #1d2e3d;
        --bg-300:      #354656;
        --radius:      10px;
        --radius-sm:   6px;
        --shadow:      0 4px 24px rgba(0,0,0,0.35);
        --shadow-sm:   0 2px 8px rgba(0,0,0,0.25);
        --transition:  0.2s ease;
      }
 
      /* ── Reset ── */
      *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
 
      body {
        font-family: 'DM Sans', sans-serif;
        background: var(--bg-100);
        color: var(--text-100);
        display: flex;
        min-height: 100vh;
        -webkit-font-smoothing: antialiased;
      }
 
      h1, h2, h3 {
        font-family: 'Sora', sans-serif;
        color: var(--text-100);
      }
 
      p { color: var(--text-200); font-size: 0.9rem; }
      hr { border: none; border-top: 1px solid var(--bg-300); margin: 1.25rem 0; }
      a  { color: var(--primary-300); text-decoration: none; }
      a:hover { opacity: 0.8; }
 
      /* ── Sidebar ── */
      .sidebar {
        width: 240px;
        min-height: 100vh;
        background: var(--bg-200);
        border-right: 1px solid var(--bg-300);
        padding: 1.75rem 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        flex-shrink: 0;
      }
 
      .sidebar h2 {
        font-size: 1.1rem;
        color: var(--primary-300);
        margin-bottom: 0.25rem;
      }
 
      .sidebar > p {
        font-size: 0.8rem;
        color: var(--text-200);
        margin-bottom: 0.5rem;
      }
 
      .sidebar nav {
        margin-top: 0.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
      }
 
      .sidebar nav p,
      .sidebar nav a p {
        color: var(--text-200);
        font-size: 0.9rem;
        padding: 0.6rem 0.85rem;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: background var(--transition), color var(--transition);
        margin: 0;
      }
 
      .sidebar nav p:hover,
      .sidebar nav a:hover p {
        background: var(--bg-300);
        color: var(--primary-300);
      }
 
      .sidebar nav a { color: var(--text-200); text-decoration: none; }
 
      /* ── Main Content ── */
      .main-content {
        flex: 1;
        padding: 2rem;
        overflow-y: auto;
      }
 
      .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--bg-300);
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
      }
 
      .header h1 {
        font-size: 1.4rem;
        color: var(--text-100);
      }
 
      /* ── Shared Button ── */
      .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.55rem 1.2rem;
        border-radius: var(--radius-sm);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all var(--transition);
        white-space: nowrap;
        background: var(--primary-100);
        color: var(--text-100);
      }
 
      .btn:hover {
        background: var(--primary-200);
        box-shadow: 0 0 0 3px rgba(74,157,156,0.25);
      }
 
      /* ── Patient Grid ── */
      .patient-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-top: 1rem;
      }
 
      .patient-card {
        background: var(--bg-200);
        padding: 1.25rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-sm);
        border-left: 4px solid var(--primary-200);
        transition: transform var(--transition), box-shadow var(--transition);
      }
 
      .patient-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
      }
 
      .patient-card h3 {
        font-size: 1rem;
        margin-bottom: 0.4rem;
        color: var(--text-100);
      }
 
      .patient-card p {
        font-size: 0.82rem;
        color: var(--text-200);
        margin-bottom: 0.25rem;
      }
 
      .patient-card .btn {
        margin-top: 0.85rem;
        width: 100%;
        font-size: 0.8rem;
        padding: 0.45rem 0.75rem;
      }
 
      /* ── Appointment Table ── */
      #appointmentSection table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        background: var(--bg-200);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
      }
 
      #appointmentSection thead {
        background: var(--primary-100);
      }
 
      #appointmentSection th {
        padding: 0.85rem 1rem;
        text-align: left;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: var(--primary-300);
      }
 
      #appointmentSection td {
        padding: 0.8rem 1rem;
        font-size: 0.88rem;
        color: var(--text-200);
        border-bottom: 1px solid var(--bg-300);
      }
 
      #appointmentSection tbody tr:hover td {
        background: var(--bg-300);
        color: var(--text-100);
      }
 
      #appointmentSection tbody tr:last-child td {
        border-bottom: none;
      }
 
      /* ── Modal Overlay ── */
      .modal {
        display: none;
        position: fixed;
        z-index: 100;
        inset: 0;
        background: rgba(0, 0, 0, 0.65);
        backdrop-filter: blur(3px);
        align-items: center;
        justify-content: center;
      }
 
      .modal.open { display: flex; }
 
      /* ── Modal Box ── */
      .modal-content {
        background: var(--bg-200);
        border: 1px solid var(--bg-300);
        border-radius: var(--radius);
        padding: 2rem;
        width: 90%;
        max-width: 560px;
        max-height: 85vh;
        overflow-y: auto;
        box-shadow: var(--shadow);
        position: relative;
      }
 
      .modal-content h2 {
        font-size: 1.2rem;
        color: var(--primary-300);
        margin-bottom: 1rem;
        padding-right: 2rem;
      }
 
      .modal-content h3 {
        font-size: 1rem;
        color: var(--text-200);
        margin-bottom: 0.75rem;
      }
 
      .close-btn {
        position: absolute;
        top: 1rem;
        right: 1.25rem;
        font-size: 1.5rem;
        font-weight: 700;
        cursor: pointer;
        color: var(--text-200);
        line-height: 1;
        background: none;
        border: none;
        transition: color var(--transition);
      }
 
      .close-btn:hover { color: var(--accent-100); }
 
      /* ── Modal Record List ── */
      #modalData ul {
        list-style: none;
        padding: 0;
        margin-bottom: 0.5rem;
      }
 
      #modalData li {
        background: var(--bg-300);
        padding: 0.85rem 1rem;
        margin-bottom: 0.6rem;
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--primary-200);
        font-size: 0.875rem;
        color: var(--text-200);
        line-height: 1.6;
      }
 
      #modalData li strong { color: var(--text-100); }
 
      #modalData hr {
        border: none;
        border-top: 1px solid var(--bg-300);
        margin: 0.75rem 0;
      }
 
      #modalData p {
        font-size: 0.875rem;
        color: var(--text-200);
        padding: 0.5rem 0;
      }
 
      /* ── Modal Form ── */
      #addRecordForm label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-200);
        margin-bottom: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
      }
 
      #addRecordForm textarea {
        width: 100%;
        padding: 0.6rem 0.85rem;
        background: var(--bg-300);
        border: 1px solid var(--bg-300);
        border-radius: var(--radius-sm);
        color: var(--text-100);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        resize: vertical;
        min-height: 70px;
        outline: none;
        transition: border-color var(--transition), box-shadow var(--transition);
        margin-bottom: 0.85rem;
      }
 
      #addRecordForm textarea:focus {
        border-color: var(--primary-200);
        box-shadow: 0 0 0 3px rgba(74,157,156,0.2);
        background: var(--bg-200);
      }
 
      #addRecordForm .btn-save {
        background: var(--primary-100);
        color: var(--text-100);
        width: 100%;
        padding: 0.65rem;
        border: 2px solid var(--primary-100);
        border-radius: var(--radius-sm);
        font-family: 'DM Sans', sans-serif;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition);
      }
 
      #addRecordForm .btn-save:hover {
        background: var(--primary-200);
        border-color: var(--primary-200);
        box-shadow: 0 0 0 3px rgba(74,157,156,0.25);
      }
 
      /* ── Scrollbar styling ── */
      ::-webkit-scrollbar { width: 6px; }
      ::-webkit-scrollbar-track { background: var(--bg-100); }
      ::-webkit-scrollbar-thumb { background: var(--bg-300); border-radius: 99px; }
      ::-webkit-scrollbar-thumb:hover { background: var(--primary-100); }
    </style>
  </head>
  <body>
    <div class="sidebar">
      <h2>Clinic Admin</h2>
      <p>Welcome, Doctor</p>
      <hr />
      <nav>
        <p style="cursor: pointer" onclick="showSection('patients')">
          📋 Patient List
        </p>
        <p style="cursor: pointer" onclick="showSection('appointments')">
          📅 Appointments
        </p>
        <a href="api/export_csv.php" style="color: white; text-decoration: none"
          ><p>📤 Export CSV</p></a
        >
      </nav>
    </div>

    <div class="main-content">
      <div class="header">
        <h1 id="viewTitle">Doctor Dashboard</h1>
        <button class="btn" onclick="location.reload()">Refresh Data</button>
      </div>

      <!-- Section 1: Patient List -->
      <div id="patientSection">
        <div id="patientList" class="patient-grid">
          <p>Loading patients...</p>
        </div>
      </div>

      <!-- Section 2: Appointment List (Now safely inside main-content) -->
      <div id="appointmentSection" style="display: none">
        <table
          border="1"
          width="100%"
          style="border-collapse: collapse; margin-top: 20px; background: white"
        >
          <thead style="background: #3498db; color: white">
            <tr>
              <th style="padding: 10px">Date</th>
              <th>Patient</th>
              <th>Time</th>
              <th>Symptoms</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="appointmentTableBody">
            <!-- Appointments will be injected here -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- The Modal -->
    <div id="patientModal" class="modal">
      <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h2 id="modalPatientName">Patient Details</h2>
        <div id="modalData">
          <!-- Records will be injected here -->
        </div>

        <!-- Person B: Add Record Form Starts Here -->
        <hr />
        <h3>Add New Medical Record</h3>
        <form id="addRecordForm">
            <!-- This hidden input is vital to tell PHP which patient we are editing -->
            <input type="hidden" id="modalPatientId"> 
            
            <div style="margin-bottom: 10px;">
                <label>Diagnosis:</label><br>
                <textarea id="diagnosis" required style="width: 100%; height: 60px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
            </div>
            
            <div style="margin-bottom: 10px;">
                <label>Treatment Plan:</label><br>
                <textarea id="treatment" required style="width: 100%; height: 60px; border-radius: 5px; border: 1px solid #ccc;"></textarea>
            </div>
            
            <button type="submit" class="btn" style="background: #2ecc71; width: 100%;">Save Medical Record</button>
        </form>
        <!-- Form Ends -->
      </div>
    </div>

    <!-- Link our logic file (we will create this in the next step) -->
    <script src="asset/js/doctor_dashboard.js"></script>
  </body>
</html>
