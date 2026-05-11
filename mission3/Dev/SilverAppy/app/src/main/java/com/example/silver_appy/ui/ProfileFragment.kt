package com.example.silver_appy.ui

import android.content.Context
import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import com.example.silver_appy.R

class ProfileFragment : Fragment() {

    private lateinit var mainViewModel: MainViewModel
    private lateinit var authViewModel: AuthViewModel

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_profile, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        mainViewModel = ViewModelProvider(requireActivity())[MainViewModel::class.java]
        authViewModel = ViewModelProvider(requireActivity())[AuthViewModel::class.java]

        val tvName = view.findViewById<TextView>(R.id.tv_user_name)
        val tvEmail = view.findViewById<TextView>(R.id.tv_user_email)
        val btnLogout = view.findViewById<Button>(R.id.btn_logout)
        val btnEdit = view.findViewById<Button>(R.id.btn_edit_profile)
        val btnBack = view.findViewById<Button>(R.id.btn_back_profile)

        mainViewModel.user.observe(viewLifecycleOwner) { currentUser ->
            if (currentUser != null) {
                tvName.text = "${currentUser.prenom} ${currentUser.nom}"
                tvEmail.text = currentUser.email
            }
        }

        btnBack.setOnClickListener {
            parentFragmentManager.popBackStack()
        }

        btnEdit.setOnClickListener {
            requireActivity().supportFragmentManager.beginTransaction()
                .replace(R.id.main_fragment_container, EditProfileFragment())
                .addToBackStack(null)
                .commit()
        }

        btnLogout.setOnClickListener {
            authViewModel.logout {
                val sharedPref = requireActivity().getSharedPreferences("SilverAppyPrefs", Context.MODE_PRIVATE)
                sharedPref.edit().putBoolean("isLoggedIn", false).apply()

                requireActivity().supportFragmentManager.beginTransaction()
                    .replace(R.id.main_fragment_container, LoginFragment())
                    .commit()
            }
        }
    }
}