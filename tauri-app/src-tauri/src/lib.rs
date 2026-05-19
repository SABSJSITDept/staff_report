use serde::Deserialize;
use std::env;
use std::fs;
use tauri::Manager;

#[derive(Deserialize)]
struct AppConfig {
    url: String,
}

fn get_config_url() -> String {
    let default_url = "http://127.0.0.1:8000".to_string();
    if let Ok(mut exe_path) = env::current_exe() {
        exe_path.pop(); // Remove the executable name, now points to its parent directory
        let config_path = exe_path.join("config.json");
        if config_path.exists() {
            if let Ok(config_str) = fs::read_to_string(config_path) {
                if let Ok(config) = serde_json::from_str::<AppConfig>(&config_str) {
                    return config.url;
                }
            }
        }
    }
    default_url
}

#[cfg_attr(mobile, tauri::mobile_entry_point)]
pub fn run() {
    tauri::Builder::default()
        .setup(|app| {
            if cfg!(debug_assertions) {
                app.handle().plugin(
                    tauri_plugin_log::Builder::default()
                        .level(log::LevelFilter::Info)
                        .build(),
                )?;
            }

            // Retrieve the main webview window and navigate to the dynamic URL from config.json
            if let Some(webview_window) = app.get_webview_window("main") {
                let url_str = get_config_url();
                if let Ok(url) = tauri::Url::parse(&url_str) {
                    let _ = webview_window.navigate(url);
                }
            }

            Ok(())
        })
        .run(tauri::generate_context!())
        .expect("error while running tauri application");
}
