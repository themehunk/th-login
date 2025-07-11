import { __ } from "@wordpress/i18n";
import { ColorPicker, GradientPicker, TextControl, RangeControl } from "@wordpress/components";
import { CustomSelectControl } from "../custom-select-control";
import {
	__experimentalToggleGroupControl as ToggleGroupControl,
	__experimentalToggleGroupControlOption as ToggleGroupControlOption,
} from '@wordpress/components';
import AccordionSection from "./accordion-section";

const BackgroundSettingsPanel = ({ title, background = {}, path = [], handleSettingChange }) => {
	const type = background.type || "color";
	const color = background.color || "#ffffff";
	const gradient = background.gradient || "";
	const image = background.image || {};
	const imageURL = image.url || "";
	const position = image.position || "center center";
	const size = image.size || "cover";
	const repeat = image.repeat || "no-repeat";
	const opacity = background.opacity ?? 1;

	return (
		<AccordionSection title={title} defaultOpen={false}>
			<ToggleGroupControl
				__nextHasNoMarginBottom={true}
				label={__("Background Type", "th-login")}
				value={type}
				isBlock
				onChange={(newType) => {
					handleSettingChange("design", [...path, "type"], newType);
				}}
			>
				<ToggleGroupControlOption value="color" label={__("Color", "th-login")} />
				<ToggleGroupControlOption value="gradient" label={__("Gradient", "th-login")} />
				<ToggleGroupControlOption value="image" label={__("Image", "th-login")} />
			</ToggleGroupControl>

			{type === "color" && (
				<div className="background-color-picker">
					<ColorPicker
						color={color}
						onChangeComplete={(value) => {
							handleSettingChange("design", [...path, "color"], value.hex);
						}}
						disableAlpha={false}
					/>
				</div>
			)}

			{type === "gradient" && (
				<div className="background-gradient-picker">
					<GradientPicker
						value={gradient}
						onChange={(value) => handleSettingChange("design", [...path, "gradient"], value)}
						gradients={[
							{
								name: __("Default", "th-login"),
								gradient: "linear-gradient(135deg,#f6d365 0%,#fda085 100%)",
								slug: "default",
							},
							{
								name: __("Ocean Blue", "th-login"),
								gradient: "linear-gradient(135deg,#2BC0E4 0%,#EAECC6 100%)",
								slug: "ocean-blue",
							},
						]}
					/>
				</div>
			)}

			{type === "image" && (
				<>
					<div className="image-url-input">
						<TextControl
							__next40pxDefaultSize
							__nextHasNoMarginBottom={true}
							label={__("Image URL", "th-login")}
							value={imageURL}
							onChange={(val) =>
								handleSettingChange("design", [...path, "image", "url"], val)
							}
							placeholder="https://example.com/image.jpg"
						/>
					</div>

					{imageURL && (
						<>
							<div style={{ marginTop: "10px" }}>
								<img
									src={imageURL}
									alt="Preview"
									style={{
										width: "100%",
										height: "auto",
										borderRadius: "6px",
										border: "1px solid #ccc",
										objectFit: "cover",
									}}
								/>
							</div>

							<div className="th-login-media-image">
								<CustomSelectControl
									label={__("Position", "th-login")}
									value={position}
									options={[
										{ label: __("Top Left", "th-login"), value: "top left" },
										{ label: __("Top Center", "th-login"), value: "top center" },
										{ label: __("Top Right", "th-login"), value: "top right" },
										{ label: __("Center Left", "th-login"), value: "center left" },
										{ label: __("Center Center", "th-login"), value: "center center" },
										{ label: __("Center Right", "th-login"), value: "center right" },
										{ label: __("Bottom Left", "th-login"), value: "bottom left" },
										{ label: __("Bottom Center", "th-login"), value: "bottom center" },
										{ label: __("Bottom Right", "th-login"), value: "bottom right" },
									]}
									onChange={(val) =>
										handleSettingChange("design", [...path, "image", "position"], val)
									}
								/>

								<CustomSelectControl
									label={__("Size", "th-login")}
									value={size}
									options={[
										{ label: __("Cover", "th-login"), value: "cover" },
										{ label: __("Contain", "th-login"), value: "contain" },
										{ label: __("Auto", "th-login"), value: "auto" },
									]}
									onChange={(val) =>
										handleSettingChange("design", [...path, "image", "size"], val)
									}
								/>

								<CustomSelectControl
									label={__("Repeat", "th-login")}
									value={repeat}
									options={[
										{ label: __("No Repeat", "th-login"), value: "no-repeat" },
										{ label: __("Repeat", "th-login"), value: "repeat" },
										{ label: __("Repeat X", "th-login"), value: "repeat-x" },
										{ label: __("Repeat Y", "th-login"), value: "repeat-y" },
									]}
									onChange={(val) =>
										handleSettingChange("design", [...path, "image", "repeat"], val)
									}
								/>
							</div>
						</>
					)}
				</>
			)}

			<div className="background-opacity-slider" style={{ marginTop: "16px" }}>
				<RangeControl
					label={__("Opacity", "th-login")}
					value={opacity}
					onChange={(val) => {
						handleSettingChange("design", [...path, "opacity"], val);
					}}
					min={0}
					max={1}
					step={0.1}
					withInputField
				/>
			</div>
		</AccordionSection>
	);
};

export default BackgroundSettingsPanel;
