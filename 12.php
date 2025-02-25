import React, { useState } from 'react';

const DoctorAppointments = () => {
  // Sample appointment data
  const [appointments, setAppointments] = useState([
    { id: 1, patientName: "John Doe", date: "2025-02-24", time: "09:00", reason: "Annual checkup", status: "Scheduled" },
    { id: 2, patientName: "Jane Smith", date: "2025-02-24", time: "10:30", reason: "Follow-up", status: "Scheduled" },
    { id: 3, patientName: "Robert Johnson", date: "2025-02-24", time: "13:15", reason: "Consultation", status: "Scheduled" },
    { id: 4, patientName: "Emily Wilson", date: "2025-02-25", time: "09:00", reason: "Blood test", status: "Scheduled" },
    { id: 5, patientName: "Michael Brown", date: "2025-02-25", time: "11:45", reason: "Vaccination", status: "Scheduled" },
    { id: 6, patientName: "Sarah Davis", date: "2025-02-26", time: "14:30", reason: "Physical therapy", status: "Scheduled" },
  ]);

  // State for filters
  const [dateFilter, setDateFilter] = useState("");
  
  // Function to handle appointment status change
  const handleStatusChange = (appointmentId, newStatus) => {
    setAppointments(appointments.map(appointment => 
      appointment.id === appointmentId ? { ...appointment, status: newStatus } : appointment
    ));
  };

  // Function to handle appointment deletion
  const handleDelete = (appointmentId) => {
    setAppointments(appointments.filter(appointment => appointment.id !== appointmentId));
  };

  // Filter appointments based on selected date
  const filteredAppointments = dateFilter 
    ? appointments.filter(appointment => appointment.date === dateFilter)
    : appointments;

  // Get unique dates for the filter dropdown
  const uniqueDates = [...new Set(appointments.map(appointment => appointment.date))];

  return (
    <div className="container mx-auto p-4">
      <h1 className="text-2xl font-bold mb-4">Doctor's Appointments</h1>
      
      {/* Filter section */}
      <div className="mb-4">
        <label className="mr-2">Filter by date:</label>
        <select 
          value={dateFilter} 
          onChange={(e) => setDateFilter(e.target.value)}
          className="border rounded p-1"
        >
          <option value="">All dates</option>
          {uniqueDates.map(date => (
            <option key={date} value={date}>{date}</option>
          ))}
        </select>
      </div>
      
      {/* Appointments table */}
      <div className="overflow-x-auto">
        <table className="min-w-full bg-white border">
          <thead>
            <tr className="bg-gray-100">
              <th className="px-4 py-2 text-left">Patient</th>
              <th className="px-4 py-2 text-left">Date</th>
              <th className="px-4 py-2 text-left">Time</th>
              <th className="px-4 py-2 text-left">Reason</th>
              <th className="px-4 py-2 text-left">Status</th>
              <th className="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            {filteredAppointments.map(appointment => (
              <tr key={appointment.id} className="border-t">
                <td className="px-4 py-2">{appointment.patientName}</td>
                <td className="px-4 py-2">{appointment.date}</td>
                <td className="px-4 py-2">{appointment.time}</td>
                <td className="px-4 py-2">{appointment.reason}</td>
                <td className="px-4 py-2">
                  <select 
                    value={appointment.status} 
                    onChange={(e) => handleStatusChange(appointment.id, e.target.value)}
                    className="border rounded p-1"
                  >
                    <option value="Scheduled">Scheduled</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                </td>
                <td className="px-4 py-2">
                  <button 
                    onClick={() => handleDelete(appointment.id)}
                    className="bg-red-500 text-white px-2 py-1 rounded"
                  >
                    Delete
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
      
      {filteredAppointments.length === 0 && (
        <p className="mt-4">No appointments found.</p>
      )}
    </div>
  );
};

export default DoctorAppointments;