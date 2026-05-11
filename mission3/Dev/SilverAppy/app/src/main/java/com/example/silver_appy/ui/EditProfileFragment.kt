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
import java.util.Calendar
import java.util.Locale

class EditProfileFragment : Fragment() {

    private lateinit var mainViewModel: MainViewModel

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_edit_profile, container, false)

        val etName = view.findViewById<EditText>(R.id.et_edit_name)
        val etEmail = view.findViewById<EditText>(R.id.et_edit_email)
        val etDob = view.findViewById<EditText>(R.id.et_edit_dob)
        val etPhone = view.findViewById<EditText>(R.id.et_edit_phone)
        val etAddress = view.findViewById<EditText>(R.id.et_edit_address)
        val etZip = view.findViewById<EditText>(R.id.et_edit_zip)
        val etCity = view.findViewById<EditText>(R.id.et_edit_city)
        val etCountry = view.findViewById<EditText>(R.id.et_edit_country)

        val btnSave = view.findViewById<Button>(R.id.btn_save_profile)
        val tvCancel = view.findViewById<TextView>(R.id.tv_cancel_edit)

        mainViewModel = ViewModelProvider(requireActivity())[MainViewModel::class.java]

        mainViewModel.user.value?.let { currentUser ->
            etName.setText("${currentUser.prenom} ${currentUser.nom}")
            etEmail.setText(currentUser.email)

            val rawDate = currentUser.dateNaissance ?: ""
            if (rawDate.length >= 10) {
                val yyyyMmDd = rawDate.substring(0, 10) // On isole "2006-08-15"
                val parts = yyyyMmDd.split("-")
                if (parts.size == 3) {
                    etDob.setText("${parts[2]}/${parts[1]}/${parts[0]}") // Format JJ/MM/AAAA
                } else {
                    etDob.setText(yyyyMmDd)
                }
            }

            etPhone.setText(currentUser.numTelephone ?: "")
            etAddress.setText(currentUser.adresse ?: "")
            etZip.setText(currentUser.codePostal ?: "")
            etCity.setText(currentUser.ville ?: "")
            etCountry.setText(currentUser.pays ?: "")
        }

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

        btnSave.setOnClickListener {
            val name = etName.text.toString()
            val email = etEmail.text.toString()
            val phone = etPhone.text.toString()
            val address = etAddress.text.toString()
            val zip = etZip.text.toString()
            val city = etCity.text.toString()
            val country = etCountry.text.toString()

            val displayDob = etDob.text.toString()
            var apiDob = displayDob
            val parts = displayDob.split("/")
            if (parts.size == 3) {
                apiDob = "${parts[2]}-${parts[1]}-${parts[0]}"
            }

            mainViewModel.updateProfile(
                fullName = name,
                email = email,
                dob = apiDob,
                phone = phone,
                address = address,
                zip = zip,
                city = city,
                country = country,
                onSuccess = {
                    Toast.makeText(context, "Informations mises à jour !", Toast.LENGTH_SHORT).show()
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