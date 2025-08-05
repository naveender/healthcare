<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class AppointmentController extends Controller
{
    public function book(Request $request)
    {   
        $data = $request->validate([
            'healthcare_professional_id' => 'required|exists:healthcare_professionals,id',
            'appointment_start_time' => 'required|date|after:now',
            'appointment_end_time' => 'required|date|after:appointment_start_time',
        ]);

        $conflict = Appointment::where('healthcare_professional_id', $data['healthcare_professional_id'])
            ->where('status', 'booked')
            ->where(function ($query) use ($data) {
                $query->whereBetween('appointment_start_time', [$data['appointment_start_time'], $data['appointment_end_time']])
                    ->orWhereBetween('appointment_end_time', [$data['appointment_start_time'], $data['appointment_end_time']]);
            })->exists();

        if ($conflict) {
            return response()->json(['message' => 'This time slot is already booked.'], 409);
        }

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'healthcare_professional_id' => $data['healthcare_professional_id'],
            'appointment_start_time' => $data['appointment_start_time'],
            'appointment_end_time' => $data['appointment_end_time'],
        ]);

        return response()->json($appointment, 201);
    }

    public function myAppointments()
    {
        return Auth::user()->appointments()->with('healthcareProfessional')->get();
    }

    public function cancel($id)
    {
        $appointment = Appointment::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if (Carbon::parse($appointment->appointment_start_time)->diffInHours(now()) < 24) {
            return response()->json(['message' => 'Cannot cancel within 24 hours.'], 403);
        }

        $appointment->status = 'cancelled';
        $appointment->save();

        return response()->json(['message' => 'Appointment cancelled.']);
    }
}
