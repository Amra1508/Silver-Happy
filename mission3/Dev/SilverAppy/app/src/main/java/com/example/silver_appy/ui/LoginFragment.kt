package com.example.silver_appy.ui

import android.content.Context
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

class LoginFragment : Fragment() {

    private lateinit var authViewModel: AuthViewModel

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        val view = inflater.inflate(R.layout.fragment_login, container, false)

        val btnLogin = view.findViewById<Button>(R.id.btn_login)
        val etIdentifiant = view.findViewById<EditText>(R.id.et_identifiant)
        val etPassword = view.findViewById<EditText>(R.id.et_password)
        val tvCreateAccount = view.findViewById<TextView>(R.id.tv_create_account)

        authViewModel = ViewModelProvider(requireActivity())[AuthViewModel::class.java]

        val sharedPref = requireActivity().getSharedPreferences("SilverAppyPrefs", Context.MODE_PRIVATE)
        val isLoggedIn = sharedPref.getBoolean("isLoggedIn", false)

        if (isLoggedIn) {
            navigateToActivities()
        }

        btnLogin.setOnClickListener {
            val email = etIdentifiant.text.toString()
            val mdp = etPassword.text.toString()

            authViewModel.login(email, mdp,
                onSuccess = {
                    sharedPref.edit().putBoolean("isLoggedIn", true).apply()
                    navigateToActivities()
                },
                onError = { erreur ->
                    Toast.makeText(context, erreur, Toast.LENGTH_SHORT).show()
                }
            )
        }

        tvCreateAccount.setOnClickListener {
            requireActivity().supportFragmentManager.beginTransaction()
                .replace(R.id.main_fragment_container, RegisterFragment())
                .addToBackStack(null)
                .commit()
        }

        return view
    }

    private fun navigateToActivities() {
        requireActivity().supportFragmentManager.beginTransaction()
            .replace(R.id.main_fragment_container, ActivitiesFragment())
            .commit()
    }
}