// 1. Fetch the list of all patients (For the Patient Management Section)
async function fetchPatients() {
    try {
        const response = await fetch('api/get_patients.php');
        const patients = await response.json();

        const listContainer = document.getElementById('patientList');
        if (!listContainer) return;
        
        listContainer.innerHTML = ''; 

        patients.forEach(patient => {
            const card = document.createElement('div');
            card.className = 'patient-card';
            card.innerHTML = `
                <h3>${patient.name}</h3>
                <p><strong>Age:</strong> ${patient.age || 'N/A'}</p>
                <p><strong>Gender:</strong> ${patient.gender || 'N/A'}</p>
                <button class="btn" onclick="viewDetails(${patient.id})">View Records</button>
            `;
            listContainer.appendChild(card);
        });
    } catch (error) {
        console.error('Error fetching patients:', error);
    }
}

// 2. Fetch Appointments (For the Upcoming Appointments Section)
async function fetchAppointments() {
    try {
        const response = await fetch('api/get_appointments.php');
        const data = await response.json();
        
        const tableBody = document.getElementById('appointmentTableBody');
        if (!tableBody) return;

        tableBody.innerHTML = ''; 

        if (data.success) {
            if (data.appointments && data.appointments.length > 0) {
                data.appointments.forEach(appo => {
                    const row = `
                        <tr style="text-align: center; border-bottom: 1px solid #ddd;">
                            <td style="padding: 10px;">${appo.appointment_date}</td>
                            <td>${appo.patient_name}</td> 
                            <td>${appo.time_slot}</td>
                            <td>${appo.symptoms || 'No symptoms provided'}</td>
                            <td><span style="color: orange; font-weight: bold;">${appo.status}</span></td>
                        </tr>`;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="5" style="padding: 20px;">No upcoming appointments.</td></tr>';
            }
        }
    } catch (error) {
        console.error('Error fetching appointments:', error);
    }
}

// 3. Navigation Logic (Switching between Patients and Appointments)
function showSection(section) {
    const pSec = document.getElementById('patientSection');
    const aSec = document.getElementById('appointmentSection');
    const title = document.getElementById('viewTitle');

    if (section === 'patients') {
        pSec.style.display = 'block';
        aSec.style.display = 'none';
        title.innerText = "Patient Management";
        fetchPatients();
    } else {
        pSec.style.display = 'none';
        aSec.style.display = 'block';
        title.innerText = "Upcoming Appointments";
        fetchAppointments(); 
    }
}

// 4. View Patient Records Modal Logic
async function viewDetails(id) {
    const modal = document.getElementById('patientModal');
    const modalData = document.getElementById('modalData');
    const modalName = document.getElementById('modalPatientName');
    const hiddenIdInput = document.getElementById('modalPatientId');

    if (hiddenIdInput) hiddenIdInput.value = id;

    try {
        const response = await fetch(`api/get_patient_details.php?id=${id}`);
        const details = await response.json();

        if (details.length > 0) {
            modalName.innerText = `Records for ${details[0].name}`;
            let recordsHTML = `<ul>`;
            details.forEach(record => {
                if(record.diagnosis) {
                    recordsHTML += `
                        <li>
                            <strong>Date:</strong> ${record.visit_date}<br>
                            <strong>Diagnosis:</strong> ${record.diagnosis}<br>
                            <strong>Treatment:</strong> ${record.treatment}
                        </li><hr>`;
                }
            });
            recordsHTML += `</ul>`;
            
            if (recordsHTML === "<ul></ul>") {
                recordsHTML = `<p>No previous medical records found.</p>`;
            }
            modalData.innerHTML = recordsHTML;
        }
        modal.style.display = "block";
    } catch (error) {
        console.error('Error fetching details:', error);
    }
}

// 5. Close Modal functions
function closeModal() {
    document.getElementById('patientModal').style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById('patientModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

// 6. Initialize on Page Load
window.onload = function() {
    fetchPatients();
};










// // Function to fetch patients from the PHP API
// async function fetchPatients() {
//     try {
//         const response = await fetch('api/get_patients.php');
//         const patients = await response.json();

//         const listContainer = document.getElementById('patientList');
//         listContainer.innerHTML = ''; // Clear the "Loading..." message

//         patients.forEach(patient => {
//             // Create a "Card" for each patient
//             const card = document.createElement('div');
//             card.className = 'patient-card';
//             card.innerHTML = `
//                 <h3>${patient.name}</h3>
//                 <p><strong>Age:</strong> ${patient.age}</p>
//                 <p><strong>Gender:</strong> ${patient.gender}</p>
//                 <button class="btn" onclick="viewDetails(${patient.id})">View Records</button>
//             `;
//             listContainer.appendChild(card);
//         });
//     } catch (error) {
//         console.error('Error fetching patients:', error);
//     }
// }

// // Function to handle clicking "View Records" (we will build the modal later)
// function viewDetails(id) {
//     alert("Fetching details for Patient ID: " + id);
// }



// // Function to fetch and show patient details in a Modal
// async function viewDetails(id) {
//     const modal = document.getElementById('patientModal');
//     const modalData = document.getElementById('modalData');
//     const modalName = document.getElementById('modalPatientName');

//     document.getElementById('modalPatientId').value = id;




//     try {
//         const response = await fetch(`api/get_patient_details.php?id=${id}`);
//         const details = await response.json();

//         if (details.length > 0) {
//             modalName.innerText = `Records for ${details[0].name}`;
            
//             // Generate HTML for the records
//             let recordsHTML = `<ul>`;
//             details.forEach(record => {
//                 if(record.diagnosis) {
//                     recordsHTML += `
//                         <li>
//                             <strong>Date:</strong> ${record.visit_date}<br>
//                             <strong>Diagnosis:</strong> ${record.diagnosis}<br>
//                             <strong>Treatment:</strong> ${record.treatment}
//                         </li><hr>`;
//                 } else {
//                     recordsHTML = `<p>No previous medical records found for this patient.</p>`;
//                 }
//             });
//             recordsHTML += `</ul>`;
            
//             modalData.innerHTML = recordsHTML;
//         }

//         // Show the modal
//         modal.style.display = "block";

//     } catch (error) {
//         console.error('Error fetching details:', error);
//     }
// }

// // Function to close the modal
// function closeModal() {
//     document.getElementById('patientModal').style.display = "none";
// }

// // Close modal if user clicks outside of the white box
// window.onclick = function(event) {
//     const modal = document.getElementById('patientModal');
//     if (event.target == modal) {
//         modal.style.display = "none";
//     }
// }



// async function showSection(section) {
//     const pSec = document.getElementById('patientSection');
//     const aSec = document.getElementById('appointmentSection');
//     const title = document.getElementById('viewTitle');

//     if (section === 'patients') {
//         pSec.style.display = 'block';
//         aSec.style.display = 'none';
//         title.innerText = "Patient Management";
//     } else {
//         pSec.style.display = 'none';
//         aSec.style.display = 'block';
//         title.innerText = "Upcoming Appointments";
//         fetchAppointments(); // Fetch data when switching
//     }
// }

// async function fetchAppointments() {
//     try {
//         const response = await fetch('api/get_appointments.php');
//         const appointments = await response.json();
//         const tableBody = document.getElementById('appointmentTableBody');
        
//         tableBody.innerHTML = ''; 

//         appointments.forEach(appo => {
//             const row = `
//                 <tr style="text-align: center; border-bottom: 1px solid #ddd;">
//                     <td style="padding: 10px;">${appo.appointment_date}</td>
//                     <td>${appo.name}</td>
//                     <td>${appo.time_slot}</td>
//                     <td>${appo.symptoms}</td>
//                     <td><span style="color: orange; font-weight: bold;">${appo.status}</span></td>
//                 </tr>`;
//             tableBody.innerHTML += row;
//         });
//     } catch (error) {
//         console.error('Error:', error);
//     }
// }

document.getElementById('addRecordForm').addEventListener('submit', async (e) => {
    e.preventDefault(); // Stop page from refreshing

    const formData = new FormData();
    formData.append('patient_id', document.getElementById('modalPatientId').value);
    formData.append('diagnosis', document.getElementById('diagnosis').value);
    formData.append('treatment', document.getElementById('treatment').value);

    const response = await fetch('api/add_record.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    if(result.status === 'success') {
        alert("Record Saved!");
        closeModal();
        // Clear the form
        e.target.reset();
    }
});





// // Run the function as soon as the page loads
// fetchPatients();


