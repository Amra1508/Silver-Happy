package com.example.silver_appy.ui

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import androidx.fragment.app.Fragment
import androidx.lifecycle.ViewModelProvider
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.silver_appy.R

class ActivitiesFragment : Fragment() {

    private lateinit var mainViewModel: MainViewModel
    private lateinit var eventsAdapter: ActivitiesAdapter
    private lateinit var servicesAdapter: ServicesAdapter
    private lateinit var conseilsAdapter: ConseilsAdapter

    override fun onCreateView(
        inflater: LayoutInflater, container: ViewGroup?,
        savedInstanceState: Bundle?
    ): View? {
        return inflater.inflate(R.layout.fragment_activities, container, false)
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)

        mainViewModel = ViewModelProvider(requireActivity())[MainViewModel::class.java]

        val rvActivities = view.findViewById<RecyclerView>(R.id.rv_activities)
        val btnProfile = view.findViewById<Button>(R.id.btn_go_profile)
        val btnTabEvents = view.findViewById<Button>(R.id.btn_tab_events)
        val btnTabServices = view.findViewById<Button>(R.id.btn_tab_services)
        val btnTabConseils = view.findViewById<Button>(R.id.btn_tab_conseils)

        rvActivities.layoutManager = LinearLayoutManager(context)

        eventsAdapter = ActivitiesAdapter(emptyList())
        servicesAdapter = ServicesAdapter(emptyList())
        conseilsAdapter = ConseilsAdapter(emptyList())

        rvActivities.adapter = eventsAdapter

        mainViewModel.activities.observe(viewLifecycleOwner) {
            eventsAdapter.updateData(it)
        }

        mainViewModel.services.observe(viewLifecycleOwner) {
            servicesAdapter.updateData(it)
        }

        mainViewModel.conseils.observe(viewLifecycleOwner) {
            conseilsAdapter.updateData(it)
        }

        mainViewModel.user.observe(viewLifecycleOwner) { currentUser ->
            if (currentUser != null) {
                mainViewModel.fetchServices(currentUser.id)
            }
        }

        mainViewModel.fetchData()
        mainViewModel.fetchConseils()

        btnTabEvents.setOnClickListener {
            rvActivities.adapter = eventsAdapter
        }

        btnTabServices.setOnClickListener {
            rvActivities.adapter = servicesAdapter
        }

        btnTabConseils.setOnClickListener {
            rvActivities.adapter = conseilsAdapter
        }

        btnProfile.setOnClickListener {
            requireActivity().supportFragmentManager.beginTransaction()
                .replace(R.id.main_fragment_container, ProfileFragment())
                .addToBackStack(null)
                .commit()
        }
    }
}