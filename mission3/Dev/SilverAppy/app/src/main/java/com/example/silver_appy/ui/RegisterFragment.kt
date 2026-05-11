package com.example.silver_appy.ui

import android.app.DatePickerDialog
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.EditText
import android.widget.TextView
import android.widget.Toast
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import com.example.silver_appy.R
import com.example.silver_appy.network.RegisterRequest
import java.util.Calendar
import java.util.Locale

class RegisterFragment : Fragment() {

    private lateinit var authViewModel: AuthViewModel

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_register, container, false)

        authViewModel = ViewModelProvider(requireActivity())[AuthViewModel::class.java]

        val etPrenom = view.findViewById<EditText>(R.id.et_reg_prenom)
        val etNom = view.findViewById<EditText>(R.id.et_reg_nom)
        val etEmail = view.findViewById<EditText>(R.id.et_reg_email)
        val etMdp = view.findViewById<EditText>(R.id.et_reg_password)
        val etDob = view.findViewById<EditText>(R.id.et_reg_dob)
        val etPhone = view.findViewById<EditText>(R.id.et_reg_phone)
        val etAddress = view.findViewById<EditText>(R.id.et_reg_address)
        val etCity = view.findViewById<EditText>(R.id.et_reg_city)
        val etZip = view.findViewById<EditText>(R.id.et_reg_zip)
        val etCountry = view.findViewById<EditText>(R.id.et_reg_country)

        val btnRegister = view.findViewById<Button>(R.id.btn_submit_register)
        val tvCancel = view.findViewById<TextView>(R.id.tv_cancel_register)

        etDob.setOnClickListener {
            val calendar = Calendar.getInstance()
            val year = calendar.get(Calendar.YEAR)
            val month = calendar.get(Calendar.MONTH)
            val day = calendar.get(Calendar.DAY_OF_MONTH)

            val datePickerDialog = DatePickerDialog(requireContext(), { _, selectedYear, selectedMonth, selectedDay ->
                val formattedDate = String.format(Locale.FRANCE, "%02d/%02d/%04d", selectedDay, selectedMonth + 1, selectedYear)
                etDob.setText(formattedDate)
            }, year, month, day)

            datePickerDialog.show()
        }

        btnRegister.setOnClickListener {
            val displayDob = etDob.text.toString()
            var apiDob = displayDob

            val parts = displayDob.split("/")
            if (parts.size == 3) {
                apiDob = "${parts[2]}-${parts[1]}-${parts[0]}"
            }

            val request = RegisterRequest(
                prenom = etPrenom.text.toString(),
                nom = etNom.text.toString(),
                email = etEmail.text.toString(),
                mdp = etMdp.text.toString(),
                dateNaissance = apiDob,
                numTelephone = etPhone.text.toString(),
                adresse = etAddress.text.toString(),
                ville = etCity.text.toString(),
                codePostal = etZip.text.toString(),
                pays = etCountry.text.toString()
            )

            authViewModel.register(request,
                onSuccess = {
                    Toast.makeText(context, "Compte créé ! Veuillez vous connecter.", Toast.LENGTH_LONG).show()
                    parentFragmentManager.popBackStack()
                },
                onError = { errorMessage ->
                    Toast.makeText(context, errorMessage, Toast.LENGTH_LONG).show()
                }
            )
        }

        tvCancel.setOnClickListener {
            parentFragmentManager.popBackStack()
        }

        return view
    }
}