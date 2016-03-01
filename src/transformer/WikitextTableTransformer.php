<?php

namespace jsoner\transformer;

use jsoner\Helper;

class WikitextTableTransformer extends AbstractTransformer
{
	public function transformZero( $options ) {
		return Helper::errorMessage("No elements to transform.", [__METHOD__, $options]);
	}

	public function transformOne( $json , $options ) {
		return $this->transformMultiple( $json, $options );
	}

	public function transformMultiple( $json , $options ) {
		// Table
		$wikitext = '{| class="wikitable sortable"' . "\n";

		// Header
		$wikitext .= $this->buildWikitextHeader( $json[0] );

		foreach ( $json as $item ) {
			$wikitext .= $this->buildWikitextRow( $item );
		}

		$wikitext .= "|}";
		return $wikitext;
	}

	private function buildWikitextHeader( $item ) {
		$header = "|-\n";
		foreach ( $item as $key => $value ) {
			$header .= '  ! ' . $key . "\n";
		}
		return $header;
	}

	private function buildWikitextRow( $item ) {
		$row = "|-\n";

		// First element in row
		$row .= '  | ' . reset( $item ) . "\n";
		unset( $item[key( $item )] );

		// Rest of the elements in a row
		foreach ( $item as $key => $value ) {
			$valueRepresentation = $value;

			if ( $value === null ) {
				$valueRepresentation = ' ';
			}

			// If an item is nested, we try to create a meaningful representation
			// by trying a list of keys. If this fails, we
			$subSelectKeys = $this->config->getItem( 'SubSelectKeysTryOrder' );
			if ( is_array( $value ) ) {
				foreach ( $subSelectKeys as $subSelectKey ) {
					if ( array_key_exists( $subSelectKey, $value ) ) {
						$valueRepresentation = $value[$subSelectKey];
						break; // Found a OK subselect value
					}
				}

				// We found no appropriate representation for a nested array
				if ( $valueRepresentation === null ) {
					$valueRepresentation = "'''''Verschachtelt'''''";
				}
			}

			$row .= "  | $valueRepresentation\n";
		}
		return $row;
	}
}
