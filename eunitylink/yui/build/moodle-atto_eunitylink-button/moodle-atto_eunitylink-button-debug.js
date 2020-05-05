YUI.add('moodle-atto_eunitylink-button', function (Y, NAME) {

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/*
 * @package    atto_eunitylink
 * @copyright  Titus Learning 2019 by Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module moodle-atto_eunitylink-button
 */

/**
 * Atto enunitylink plugin.
 *
 * @namespace M.atto_eunitylink
 * @class button
 * @extends M.editor_atto.EditorPlugin
 */

var COMPONENTNAME = 'atto_eunitylink',
    CSS = {
        LINKTEXT: 'linktext',
        ACCESSIONNUMBER: 'accessionnumber'
    },
    // The structure of the dialog.
    TEMPLATE = '' +
        '<form class="atto_form">' +
            '<div class="m-b-1 form-group">' +
                '<label for="{{CSS.LINKTEXT}}">{{get_string "linktext" component}}</label>' +
                '<input class="form-control fullwidth {{CSS.LINKTEXT}}" type="text" ' +
                'id="{{CSS.LINKTEXT}}" value="{{selectedtext}}"  size="32"/>' +
            '</div>' +
            '<div class="m-b-1 form-group">' +
                '<label for="{{CSS.ACCESSIONNUMBER}}">' +
                    '{{get_string "accessionnumber" component}}</label>' +
                    '<input class="form-control fullwidth {{CSS.ACCESSIONNUMBER}}"' +
                    ' name="{{CSS.ACCESSIONNUMBER}}" id="{{CSS.ACCESSIONNUMBER}}" type="text"' +
                    ' value="{{accessionnumber}}" size="32"/>' +
            '</div>' +
            '<div class="mdl-align">' +
                '<br/>' +
                '<button type="submit" title = {{get_string "createlink" component}}' +
                ' class="btn btn-default submit">{{get_string "createlink" component}}</button>' +
            '</div>' +
        '</form>';
        Y.namespace('M.atto_eunitylink').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {

        /**
         * A reference to the current selection at the time that the dialogue
         * was opened.
         *
         * @property _currentSelection
         * @type Range
         * @private
         */
        _currentSelection: null,

        /**
         * Stores value brought back from db
         * when editing existing link
         */
        _accessionNumber: null,

        /**
         * A reference to the dialogue content.
         *
         * @property _content
         * @type Node
         * @private
         */
        _content: null,

        /**
         * The text initial selected tet
         *
         * @param _selectedText
         * @type String
         * @private
         */
        _selectedText: null,

        initializer: function() {
        // If we don't have the capability to view then give up.
        if (this.get('disabled')) {
            return;
        }
        // Set name of button icon to be loaded.
        var icon = 'iconone';
        this.addButton({
            tags: 'IL, strong',
            icon: 'ed/' + icon,
            iconComponent: 'atto_eunitylink',
            buttonName: icon,
            exec: 'ilink',
            callback: this._displayDialogue
        });

        },

        /**
         * Display the dialog form.
         *
         * @method _displayDialogue
         * @private
         */
        _displayDialogue: function() {
            // Store the current selection.
            this._currentSelection = this.get('host').getSelection();
            if (this._currentSelection === false || this._currentSelection.collapsed) {
                return;
            }

            var selectednode = this.get('host').getSelectionParentNode();
            if(this._currentSelection.toString() === "" || (!selectednode)){
                return;
            }

            var anchornodes = this._findSelectedAnchors(Y.one(selectednode));
            var hash = this._getLinkHash();
            if (anchornodes.length > 0 && hash) {
                var dbvals = this._getDbVals(hash);
                this._accessionNumber = dbvals.result.accessionnumber;
            } else {
                this._accessionNumber = null;
            }

            var dialogue = this.getDialogue({
                headerContent: M.util.get_string('dialogtitle', COMPONENTNAME),
                width: 'auto',
                selectedtext: this._currentSelection,
                accessionnumber: this._accessionnumber,
                focusAfterHide: true
            });

            // Set the dialogue content, and then show the dialogue.
            dialogue.set('bodyContent', this._getDialogueContent());

            dialogue.show();
        },

        /**
         * Get the hash item from a url
         * querystring
         * @returns {string}
         */
        _getLinkHash: function() {
            var selectednode = this.get('host').getSelectionParentNode();
            var anchornodes = this._findSelectedAnchors(Y.one(selectednode));
            var anchornode = anchornodes[0];
            if (!selectednode.wholeText || (!anchornode)) {
                return '';
            }
            var url = anchornode.getAttribute('href');
            var parts = this._getUrlVars(url);
            return parts.hash;
        },

        /**
         * Get row from the database via an ajax call
         * dbVals seems a bit of a generic name
         * @param {string} hash
         * @returns {array}
         */
        _getDbVals: function(hash) {
            var dbVals;
            YUI().use("io-base", function(Y) {
                // Create a configuration object for the synchronous transaction.
                var params = {
                    action: 'get_dbvals',
                    hash: hash,
                    contextid: M.cfg.contextid
                };
                var cfg = {
                    sync: true,
                    data: params,
                    arguments: {
                        context: this,
                        timeout: 500,
                        method: 'POST'
                    }
                };
                var uri = M.cfg.wwwroot + '/local/linkproxy/rest.php';
                var request = Y.io(uri, cfg);
                dbVals = JSON.parse(request.responseText);

            });
            return dbVals ? dbVals : {};
        },

        /**
         * Javascript has built in functions to do this, but they
         * are not supported by IE 11.
         * @param {string} url
         * @returns {object}
         */
        _getUrlVars: function(url) {
            var vars = {};
                url.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
                vars[key] = value;
            });
            return vars;
        },
        /**
     * Look up and down for the nearest anchor tags that are least partly contained in the selection.
     *
     * @method _findSelectedAnchors
     * @param {Node} node The node to search under for the selected anchor.
     * @return {Node|Boolean} The Node, or false if not found.
     * @private
     */
    _findSelectedAnchors: function(node) {
        var tagname = node.get('tagName'),
            hit, hits;

        // Direct hit.
        if (tagname && tagname.toLowerCase() === 'a') {
            return [node];
        }

        // Search down but check that each node is part of the selection.
        hits = [];
        node.all('a').each(function(n) {
            if (!hit && this.get('host').selectionContainsNode(n)) {
                hits.push(n);
            }
        }, this);
        if (hits.length > 0) {
            return hits;
        }
        // Search up.
        hit = node.ancestor('a');
        if (hit) {
            return [hit];
        }
        return [];
    },

        /**
         * The link was inserted, so make changes to the editor source.
         *
         * @method _setLink
         * @param {EventFacade} e
         * @private
         */
        _setLink: function(e) {
            e.preventDefault();
            this.getDialogue({
                focusAfterHide: null
            }).hide();

            var linktext = this._content.one(".linktext").get("value");

            if (linktext !== '') {
                // Add the link.
                this._setLinkOnSelection();
            }
        },

        /**
         * Final step setting the anchor on the selection.
         *
         * @private
         * @method _setLinkOnSelection
         * @param  {String} url URL the link will point to.
         * @return {Node | boolean} The added Node.
         */
        _setLinkOnSelection: function() {
            var host = this.get('host'),
                link,
                selectednode;
            this.editor.focus();
            host.setSelection(this._currentSelection);

            var accessionnumber = this._content.one(".accessionnumber").get("value");
            var _linkHash = this._getLinkHash();

            YUI().use("io-base", function(Y) {
                // Create a configuration object for the synchronous transaction.
                var params = {
                    action: 'upsert_link',
                    an: accessionnumber,
                    hash: _linkHash,
                    contextid: M.cfg.contextid
                };
                var cfg = {
                    sync: true,
                    data: params,
                    arguments: {
                        context: this,
                        timeout: 500,
                        method: 'POST'
                    }
                };
                var uri = M.cfg.wwwroot + '/local/linkproxy/rest.php';
                var request = Y.io(uri, cfg);
                 _linkHash = JSON.parse(request.responseText);
            });

            var hyperlink = M.cfg.wwwroot + '/local/linkproxy/rest.php?action=get_link&hash=' + _linkHash.result;
            var selection = document.getSelection();
            if (this._currentSelection[0].collapsed) {
                // Firefox cannot add links when the selection is empty so we will add it manually.
                link = Y.Node.create('<a>' + hyperlink + '</a>');
                link.setAttribute('href', hyperlink);

                // Add the node and select it to replicate the behaviour of execCommand.
                selectednode = host.insertContentAtFocusPoint(link.get('outerHTML'));
                host.setSelection(host.getSelectionFromNode(selectednode));
                selection.anchorNode.parentElement.target = '_blank';

            } else {
                var sel = window.getSelection();
                var replacementText = this._content.one(".linktext").get("value");
                if (sel.rangeCount) {
                    var range = sel.getRangeAt(0);
                    range.deleteContents();
                    range.insertNode(document.createTextNode(replacementText));
                }
                document.execCommand('unlink', false, null);
                document.execCommand('createLink', false, hyperlink);
                selection.anchorNode.parentElement.target = '_blank';
                // Now set the target.
                selectednode = host.getSelectionParentNode();
            }

            // Note this is a document fragment and YUI doesn't like them.
            if (!selectednode.wholeText) {
                return false;
            }

            return selectednode;
        },

        /**
         * Generates the content of the dialogue.
         *
         * @method _getDialogueContent
         * @return {Node} Node containing the dialogue content
         * @private
         */
        _getDialogueContent: function() {
            var template = Y.Handlebars.compile(TEMPLATE);
            // If no text is selected clear form values.
            if (!this._currentSelection) {
                this._accessionNumber = null;
            }
            this._content = Y.Node.create(template({
                component: COMPONENTNAME,
                selectedtext: this._currentSelection,
                accessionnumber: this._accessionNumber,
                CSS: CSS
            }));

            this._content.one('.submit').on('click', this._setLink, this);

            return this._content;
        }
    }, {
        ATTRS: {
            disabled: {
                value: false
            },
            hosturl: {
                value: ''
            },
            queryparams: {
                value: ''
            }

        }
    });


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
