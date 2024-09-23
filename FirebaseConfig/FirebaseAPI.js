
// DOM EVENT LISTNER

// document.addEventListener('DOMContentLoaded', function() {
//   // Check if the form exists before adding event listener
 

  

//   //Section
//   GetSectionList();
//   const datatablesSectionList = document.getElementById("datatablesSectionList");
//   if (datatablesSectionList) {
//         document.getElementById('datatablesSectionList').addEventListener('click', function (e) {
//           if (e.target.classList.contains('edit-btn')) {
//               const id = e.target.getAttribute('data-id');
//               window.location.href = `SectionAdd.php?id=${id}`;
//           }
          
//           if (e.target.classList.contains('delete-btn')) {
//               const id = e.target.getAttribute('data-id');
            
//               console.log('Delete button clicked for ID:', id);
            
//           }
//       });
//   } 
 

// });

// DOM EVENT LISTNER





function GetDateFromusage_GameStatsInfo() {
    firebase
      .database()
      .ref("usage_GameStatsInfo/")
      .on("value", function (snap) {
        const data = snap.val();
        if (data) {
          // Iterate over each child node in the snapshot
          Object.keys(data).forEach(key => {
            const childData = data[key];
            console.log("Key:", key);
  
            // Access each field from childData
            const appLogo = childData.appLogo;
            const appName = childData.appName;
            const date = childData.date;
            const totalTimeInForeground = childData.totalTimeInForeground;
            const userId = childData.userId;
            const userIdDate = childData.userIdDate;
  
            console.log("App Logo:", appLogo);
            console.log("App Name:", appName);
            console.log("Date:", date);
            console.log("Total Time In Foreground:", totalTimeInForeground);
            console.log("User ID:", userId);
            console.log("User ID Date:", userIdDate);
  
            // Process or display the data as needed
            // For example, you might want to render this data into your UI
          });
        }
      });
  }



  function GetUserInfo() {
    const tableBody = document.querySelector('#ApplicationUsers tbody');
    firebase
      .database()
      .ref("user_profile/")
      .on("value", function (snap) {
        const data = snap.val();
        tableBody.innerHTML = '';
        if (data) {
          // Iterate over each child node in the snapshot
          Object.keys(data).forEach(key => {
            const childData = data[key];
            const row = document.createElement('tr');
            row.innerHTML = `
            <td>${childData.email}</td>
            <td>${childData.studentName}</td>
            <td>${childData.parentName}</td>
            <td>${childData.section}</td>
            <td>${childData.gender}</td>
          `;
          tableBody.appendChild(row);

            // Access each field from childData
            // const email = childData.email;
            // const gender = childData.gender;
            // const parentName = childData.parentName;
            // const section = childData.section;
            // const uid = childData.uid;
          });
        }
      });
  }
  
 


//SECTION 

//REadSection
// function readSection() {
//   const sectionName = document.getElementById("sectionName").value.trim();
//   const sectionDescription = document.getElementById("sectionDescription").value.trim();
//   const UID = Date.now().toString();
//   return { UID,sectionName, sectionDescription };
// }

// // SectionTable
// function GetSectionList() {
//   const tableBody = document.querySelector('#datatablesSectionList tbody');
//   const datatablesSectionList = document.getElementById('datatablesSectionList');

//   if (!tableBody || !datatablesSectionList) {
//     console.error('Table body or table element not found');
//     return;
//   }

//   // Fetch data from Firebase
//   firebase
//     .database()
//     .ref("user_section/")
//     .on("value", function (snap) {
//       const data = snap.val();
//       tableBody.innerHTML = ''; // Clear existing rows

//       if (data) {
//         // Iterate over each child node in the snapshot
//         Object.keys(data).forEach(key => {
//           const childData = data[key];
//           const row = document.createElement('tr');

//           // Create edit and delete buttons with FontAwesome icons
//           const editButton = `
//             <button class="btn btn-primary btn-sm" onclick="window.location.href='SectionAdd.php?id=${childData.UID}'">
//               <i class="fas fa-pencil-alt"></i>
//             </button>`;
//           const deleteButton = `
//             <button class="btn btn-danger btn-sm delete-btn" data-id="${childData.UID}">
//               <i class="fas fa-trash"></i>
//             </button>`;

//             row.innerHTML = `
//             <td>${childData.sectionName || 'N/A'}</td>
//             <td>${childData.sectionDescription || 'N/A'}</td>
            
//             <td>
//             ${editButton}
//             ${deleteButton}
//             </td>`;

//           tableBody.appendChild(row);
//         });

//         // Initialize or reinitialize the DataTable after adding rows
//         new simpleDatatables.DataTable(datatablesSectionList);

//         datatablesSectionList.style.display = 'table';
//       }
//     });
// }






















// var rollV, nameV, genderV, addressV;

// function readFom() {
//   rollV = document.getElementById("roll").value;
//   nameV = document.getElementById("name").value;
//   genderV = document.getElementById("gender").value;
//   addressV = document.getElementById("address").value;
//   console.log(rollV, nameV, addressV, genderV);
// }

// document.getElementById("insert").onclick = function () {
//   readFom();

//   firebase
//     .database()
//     .ref("student/" + rollV)
//     .set({
//       rollNo: rollV,
//       name: nameV,
//       gender: genderV,
//       address: addressV,
//     });
//   alert("Data Inserted");
//   document.getElementById("roll").value = "";
//   document.getElementById("name").value = "";
//   document.getElementById("gender").value = "";
//   document.getElementById("address").value = "";
// };



// document.getElementById("update").onclick = function () {
//   readFom();

//   firebase
//     .database()
//     .ref("student/" + rollV)
//     .update({
//       //   rollNo: rollV,
//       name: nameV,
//       gender: genderV,
//       address: addressV,
//     });
//   alert("Data Update");
//   document.getElementById("roll").value = "";
//   document.getElementById("name").value = "";
//   document.getElementById("gender").value = "";
//   document.getElementById("address").value = "";
// };
// document.getElementById("delete").onclick = function () {
//   readFom();

//   firebase
//     .database()
//     .ref("student/" + rollV)
//     .remove();
//   alert("Data Deleted");
//   document.getElementById("roll").value = "";
//   document.getElementById("name").value = "";
//   document.getElementById("gender").value = "";
//   document.getElementById("address").value = "";
// };
