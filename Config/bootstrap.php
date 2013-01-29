<?php
/**
 * Component
 *
 * This plugin's component will be loaded in ALL controllers.
 */
	Croogo::hookComponent('*', 'BlockAnywhere.BlockAnywhere');

/**
 * Helper
 *
 * This plugin's helper will be loaded via NodesController.
 */
	Croogo::hookHelper('Nodes', 'BlockAnywhere.Block');