import { __ } from "@wordpress/i18n";
import {
  Button,
  Spinner,
  TextareaControl,
  Dashicon,
  Notice,
} from "@wordpress/components";
import { useEffect, useState } from "@wordpress/element";

const ToolsSettings = ({
  settings,
  exportedSettings,
  setExportedSettings,
  importSettingsText,
  setImportSettingsText,
  handleExportSettings,
  handleImportSettings,
  isSaving,
  setIsResetConfirmOpen,
}) => {
  const [copied, setCopied] = useState(false);
  const [clipboardContent, setClipboardContent] = useState("");

  useEffect(() => {
    const checkClipboard = async () => {
      try {
        const text = await navigator.clipboard.readText();
        setClipboardContent(text);
      } catch (e) {
        setClipboardContent("");
      }
    };

    const onFocus = () => checkClipboard();
    window.addEventListener("focus", onFocus);
    checkClipboard();

    return () => {
      window.removeEventListener("focus", onFocus);
    };
  }, []);

  const handleCopyToClipboard = async () => {
    try {
      await navigator.clipboard.writeText(exportedSettings);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    } catch (e) {
      console.error("Failed to copy", e);
    }
  };

  const handlePasteFromClipboard = async () => {
    try {
      const text = await navigator.clipboard.readText();
      setImportSettingsText(text);
    } catch (e) {
      console.error("Failed to paste", e);
    }
  };

  return (
    <section className="settings-section">
      <div className="settings-card">
        <h2 className="section-title">
          <i className="dashicons dashicons-admin-tools"></i>
          {__("Tools", "thlogin")}
        </h2>

        {/* Export Section */}
        <div className="settings-group">
          <h3 className="group-title">{__("Export Settings", "thlogin")}</h3>

          <div className="setting-row">
            <div className="setting-label">
              <h4>{__("Export Current Settings", "thlogin")}</h4>
              <p className="description">
                {__("Backup your current configuration", "thlogin")}
              </p>
            </div>
            <div className="setting-control">
              <Button isSecondary onClick={handleExportSettings}>
                <Dashicon icon="download" />
                {__("Export Settings", "thlogin")}
              </Button>
            </div>
          </div>

          {exportedSettings && (
            <>
              <div className="setting-row">
                <div className="setting-label">
                  <h4>{__("Exported Settings", "thlogin")}</h4>
                  <p className="description">
                    {__("Copy this JSON to save your settings", "thlogin")}
                  </p>
                </div>
                <div className="setting-control">
                  <TextareaControl
                  __nextHasNoMarginBottom={true}
                    value={exportedSettings}
                    readOnly
                    rows={10}
                    className="export-textarea"
                  />
                  <Button
                    className={`copy-button ${copied ? "copied" : ""}`}
                    onClick={handleCopyToClipboard}
                    isSmall
                  >
                    {copied ? __("Copied!", "thlogin") : __("Copy", "thlogin")}
                  </Button>
                </div>
              </div>
            </>
          )}
        </div>

        {/* Import Section */}
        <div className="settings-group">
          <h3 className="group-title">{__("Import Settings", "thlogin")}</h3>

          <div className="setting-row">
            <div className="setting-label">
              <h4>{__("Import Settings", "thlogin")}</h4>
              <p className="description">
                {__("Paste previously exported settings JSON", "thlogin")}
              </p>
            </div>
            <div className="setting-control">
              <TextareaControl
              __nextHasNoMarginBottom={true}
                value={importSettingsText}
                onChange={(newValue) => setImportSettingsText(newValue)}
                rows={10}
                className="import-textarea"
              />
              <Button
                isSecondary
                className="paste-button"
                onClick={handlePasteFromClipboard}
                disabled={!clipboardContent.trim()}
              >
                <Dashicon icon="clipboard" />
                {__("Paste from Clipboard", "thlogin")}
              </Button>
            </div>
          </div>

          <div className="setting-row">
            <div className="setting-control">
              <Button
                isPrimary
                onClick={handleImportSettings}
                disabled={isSaving || !importSettingsText.trim()}
              >
                {isSaving ? (
                  <Spinner />
                ) : (
                  <>
                    <Dashicon icon="upload" />
                    {__("Import Settings", "thlogin")}
                  </>
                )}
              </Button>
            </div>
          </div>
        </div>

        {/* Reset Section */}
        <div className="settings-group">
          <h3 className="group-title">{__("Reset Settings", "thlogin")}</h3>

          <div className="setting-row">
            <div className="setting-label">
              <h4>{__("Reset All Settings", "thlogin")}</h4>
              <p className="description">
                {__("Restore all settings to default values", "thlogin")}
              </p>
            </div>
            <div className="setting-control">
              <Button
                isDestructive
                onClick={() => setIsResetConfirmOpen(true)}
              >
                <Dashicon icon="undo" />
                {__("Reset Settings", "thlogin")}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ToolsSettings;
