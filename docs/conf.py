#import sphinx_rtd_theme
import json
extensions = [
    'myst_parser',
    "sphinxext.opengraph",
    "sphinx_copybutton",
    "sphinx_panels",
#    'sphinx_rtd_theme',
]
project = 'Madis'
copyright = 'Madis Soluris'
author = 'Soluris'
language = "fr"
html_theme = 'sphinx_rtd_theme'
#html_logo = "_images/Madis-Logo.png"
html_title = "Documentation Madis"
html_theme_options = {
    'display_version': True,
    'prev_next_buttons_location': 'bottom',
    # Toc options
    'collapse_navigation': True,
    'sticky_navigation': True,
    'navigation_depth': 4,
    'includehidden': True,
    'titles_only': False
}


#html_theme = "sphinx_rtd_theme"
#html_theme_options = {
#    'analytics_id': 'G-XXXXXXXXXX',  #  Provided by Google in your dashboard
#    'analytics_anonymize_ip': False,
#    'logo_only': False,
#    #'html_logo' : 'image/logo_madis_2020_blanc.png',
#    'display_version': False,
#    'prev_next_buttons_location': 'bottom',
#    'style_external_links': False,
#    'vcs_pageview_mode': '',
#    'style_nav_header_background': 'blue',
#    # Toc options
#    'collapse_navigation': True,
#    'sticky_navigation': True,
#    'navigation_depth': 4,
#    'includehidden': True,
#    'titles_only': False
#}

