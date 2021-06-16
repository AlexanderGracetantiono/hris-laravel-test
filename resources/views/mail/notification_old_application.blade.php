<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="format-detection" content="telephone=no" /> <!-- disable auto telephone linking in iOS -->
	<title>CekOri Application Update Notification Email</title>
	<style type="text/css">
		/* RESET STYLES */
		html {
			background-color: #E1E1E1;
			margin: 0;
			padding: 0;
		}

		body,
		#bodyTable,
		#bodyCell,
		#bodyCell {
			height: 100% !important;
			margin: 0;
			padding: 0;
			width: 100% !important;
			font-family: 'Avenir Next';
		}

		table {
			border-collapse: collapse;
		}

		table[id=bodyTable] {
			width: 100% !important;
			margin: auto;
			max-width: 500px !important;
			color: #7A7A7A;
			font-weight: normal;
		}

		img,
		a img {
			border: 0;
			outline: none;
			text-decoration: none;
			height: auto;
			line-height: 100%;
		}

		a {
			text-decoration: none !important;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			color: #5F5F5F;
			font-weight: normal;
			font-family: 'Avenir Next', Helvetica, Arial, sans-serif;
			;
			font-size: 20px;
			line-height: 125%;
			text-align: Left;
			letter-spacing: normal;
			margin-top: 0;
			margin-right: 0;
			margin-bottom: 10px;
			margin-left: 0;
			padding-top: 0;
			padding-bottom: 0;
			padding-left: 0;
			padding-right: 0;
		}

		/* CLIENT-SPECIFIC STYLES */
		.ReadMsgBody {
			width: 100%;
		}

		.ExternalClass {
			width: 100%;
		}

		/* Force Hotmail/Outlook.com to display emails at full width. */
		.ExternalClass,
		.ExternalClass p,
		.ExternalClass span,
		.ExternalClass font,
		.ExternalClass td,
		.ExternalClass div {
			line-height: 100%;
		}

		/* Force Hotmail/Outlook.com to display line heights normally. */
		table,
		td {
			mso-table-lspace: 0pt;
			mso-table-rspace: 0pt;
		}

		/* Remove spacing between tables in Outlook 2007 and up. */
		#outlook a {
			padding: 0;
		}

		/* Force Outlook 2007 and up to provide a "view in browser" message. */
		img {
			-ms-interpolation-mode: bicubic;
			display: block;
			outline: none;
			text-decoration: none;
		}

		/* Force IE to smoothly render resized images. */
		body,
		table,
		td,
		p,
		a,
		li,
		blockquote {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
			font-weight: normal !important;
		}

		/* Prevent Windows- and Webkit-based mobile platforms from changing declared text sizes. */
		.ExternalClass td[class="ecxflexibleContainerBox"] h3 {
			padding-top: 10px !important;
		}

		/* Force hotmail to push 2-grid sub headers down */

		/* /\/\/\/\/\/\/\/\/ TEMPLATE STYLES /\/\/\/\/\/\/\/\/ */

		/* ========== Page Styles ========== */
		h1 {
			display: block;
			font-size: 26px;
			font-style: normal;
			font-weight: normal;
			line-height: 100%;
		}

		h2 {
			display: block;
			font-size: 20px;
			font-style: normal;
			font-weight: normal;
			line-height: 120%;
		}

		h3 {
			display: block;
			font-size: 17px;
			font-style: normal;
			font-weight: normal;
			line-height: 110%;
		}

		h4 {
			display: block;
			font-size: 18px;
			font-style: italic;
			font-weight: normal;
			line-height: 100%;
		}

		.flexibleImage {
			height: auto;
		}

		.linkRemoveBorder {
			border-bottom: 0 !important;
		}

		table[class=flexibleContainerCellDivider] {
			padding-bottom: 0 !important;
			padding-top: 0 !important;
		}

		body,
		#bodyTable {
			background-color: #E1E1E1;
		}

		#emailHeader {
			background-color: #E1E1E1;
		}

		#emailBody {
			background-color: #FFFFFF;
		}

		#emailFooter {
			background-color: #E1E1E1;
		}

		.nestedContainer {
			background-color: #F8F8F8;
			border: 1px solid #CCCCCC;
		}

		.emailButton {
			background: #91191b;
			border-collapse: separate;
		}

		.buttonContent {
			color: #FFFFFF;
			font-family: 'Avenir Next', Helvetica, Arial, sans-serif;
			;
			font-size: 18px;
			font-weight: bold;
			line-height: 100%;
			padding: 15px;
			text-align: center;
		}

		.buttonContent a {
			color: #FFFFFF;
			display: block;
			text-decoration: none !important;
			border: 0 !important;
		}

		.buttonContent:hover {
			cursor: pointer;
		}

		.emailCalendar {
			background-color: #FFFFFF;
			border: 1px solid #CCCCCC;
		}

		.emailCalendarMonth {
			background-color: #205478;
			color: #FFFFFF;
			font-family: 'Avenir Next', Helvetica, Arial, sans-serif;
			;
			font-size: 16px;
			font-weight: bold;
			padding-top: 10px;
			padding-bottom: 10px;
			text-align: center;
		}

		.emailCalendarDay {
			color: #205478;
			font-family: 'Avenir Next', Helvetica, Arial, sans-serif;
			;
			font-size: 60px;
			font-weight: bold;
			line-height: 100%;
			padding-top: 20px;
			padding-bottom: 20px;
			text-align: center;
		}

		.imageContentText {
			margin-top: 10px;
			line-height: 0;
		}

		.imageContentText a {
			line-height: 0;
		}

		#invisibleIntroduction {
			display: none !important;
		}

		/* Removing the introduction text from the view */

		/*FRAMEWORK HACKS & OVERRIDES */
		span[class=ios-color-hack] a {
			color: #275100 !important;
			text-decoration: none !important;
		}

		/* Remove all link colors in IOS (below are duplicates based on the color preference) */
		span[class=ios-color-hack2] a {
			color: #205478 !important;
			text-decoration: none !important;
		}

		span[class=ios-color-hack3] a {
			color: #8B8B8B !important;
			text-decoration: none !important;
		}

		/* A nice and clean way to target phone numbers you want clickable and avoid a mobile phone from linking other numbers that look like, but are not phone numbers.  Use these two blocks of code to "unstyle" any numbers that may be linked.  The second block gives you a class to apply with a span tag to the numbers you would like linked and styled.
			Inspired by Campaign Monitor's article on using phone numbers in email: http://www.campaignmonitor.com/blog/post/3571/using-phone-numbers-in-html-email/.
			*/
		.a[href^="tel"],
		a[href^="sms"] {
			text-decoration: none !important;
			color: #606060 !important;
			pointer-events: none !important;
			cursor: default !important;
		}

		.mobile_link a[href^="tel"],
		.mobile_link a[href^="sms"] {
			text-decoration: none !important;
			color: #606060 !important;
			pointer-events: auto !important;
			cursor: default !important;
		}


		/* MOBILE STYLES */
		@media only screen and (max-width: 480px) {

			/*////// CLIENT-SPECIFIC STYLES //////*/
			body {
				width: 100% !important;
				min-width: 100% !important;
			}

			/* Force iOS Mail to render the email at full width. */

			/* FRAMEWORK STYLES */
			/*
				CSS selectors are written in attribute
				selector format to prevent Yahoo Mail
				from rendering media query styles on
				desktop.
				*/
			/*td[class="textContent"], td[class="flexibleContainerCell"] { width: 100%; padding-left: 10px !important; padding-right: 10px !important; }*/
			table[id="emailHeader"],
			table[id="emailBody"],
			table[id="emailFooter"],
			table[class="flexibleContainer"],
			td[class="flexibleContainerCell"] {
				width: 100% !important;
			}

			td[class="flexibleContainerBox"],
			td[class="flexibleContainerBox"] table {
				display: block;
				width: 100%;
				text-align: left;
			}

			/*
				The following style rule makes any
				image classed with 'flexibleImage'
				fluid when the query activates.
				Make sure you add an inline max-width
				to those images to prevent them
				from blowing out.
				*/
			td[class="imageContent"] img {
				height: auto !important;
				width: 100% !important;
				max-width: 100% !important;
			}

			img[class="flexibleImage"] {
				height: auto !important;
				width: 100% !important;
				max-width: 100% !important;
			}

			img[class="flexibleImageSmall"] {
				height: auto !important;
				width: auto !important;
			}


			/*
				Create top space for every second element in a block
				*/
			table[class="flexibleContainerBoxNext"] {
				padding-top: 10px !important;
			}

			/*
				Make buttons in the email span the
				full width of their container, allowing
				for left- or right-handed ease of use.
				*/
			table[class="emailButton"] {
				width: 100% !important;
			}

			td[class="buttonContent"] {
				padding: 0 !important;
			}

			td[class="buttonContent"] a {
				padding: 15px !important;
			}

		}

		/*  CONDITIONS FOR ANDROID DEVICES ONLY
			*   http://developer.android.com/guide/webapps/targeting.html
			*   http://pugetworks.com/2011/04/css-media-queries-for-targeting-different-mobile-devices/ ;
			=====================================================*/

		@media only screen and (-webkit-device-pixel-ratio:.75) {
			/* Put CSS for low density (ldpi) Android layouts in here */
		}

		@media only screen and (-webkit-device-pixel-ratio:1) {
			/* Put CSS for medium density (mdpi) Android layouts in here */
		}

		@media only screen and (-webkit-device-pixel-ratio:1.5) {
			/* Put CSS for high density (hdpi) Android layouts in here */
		}

		/* end Android targeting */

		/* CONDITIONS FOR IOS DEVICES ONLY
			=====================================================*/
		@media only screen and (min-device-width : 320px) and (max-device-width:568px) {}

		/* end IOS targeting */
		.social-media-link {
			color: #fff;
			text-decoration: none;
		}

		.social-media-link:hover,
		.social-media-link:focus {
			color: #91191b;
		}

		.emailButton:hover,
		.emailButton:focus {
			background: #DD7D00;
		}
	</style>
	<!--
			Outlook Conditional CSS

			These two style blocks target Outlook 2007 & 2010 specifically, forcing
			columns into a single vertical stack as on mobile clients. This is
			primarily done to avoid the 'page break bug' and is optional.

			More information here:
			http://templates.mailchimp.com/development/css/outlook-conditional-css
		-->
	<!--[if mso 12]>
			<style type="text/css">
				.flexibleContainer{display:block !important; width:100% !important;}
			</style>
		<![endif]-->
	<!--[if mso 14]>
			<style type="text/css">
				.flexibleContainer{display:block !important; width:100% !important;}
			</style>
		<![endif]-->
</head>

<body bgcolor="#E1E1E1" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">

	<!-- CENTER THE EMAIL // -->
	<!--
		1.  The center tag should normally put all the
			content in the middle of the email page.
			I added "table-layout: fixed;" style to force
			yahoomail which by default put the content left.

		2.  For hotmail and yahoomail, the contents of
			the email starts from this center, so we try to
			apply necessary styling e.g. background-color.
		-->
	<center style="background-color:#E1E1E1;">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important;">
			<tr>
				<td align="center" valign="top" id="bodyCell">

					<!-- EMAIL HEADER // -->
					<!--
							The table "emailBody" is the email's container.
							Its width can be set to 100% for a color band
							that spans the width of the page.
						-->
					<table bgcolor="#E1E1E1" border="0" cellpadding="0" cellspacing="0" width="500" id="emailHeader">

						<!-- HEADER ROW // -->
						<tr>
							<td align="center" valign="top">
								<!-- CENTERING TABLE // -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td align="center" valign="top">
											<!-- FLEXIBLE CONTAINER // -->
											<table border="0" cellpadding="10" cellspacing="0" width="500" class="flexibleContainer">
												<tr>
													<td valign="top" width="500" class="flexibleContainerCell">

														<!-- CONTENT TABLE // -->
														<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
															<tr>
																<!--
																		The "invisibleIntroduction" is the text used for short preview
																		of the email before the user opens it (50 characters max). Sometimes,
																		you do not want to show this message depending on your design but this
																		text is highly recommended.

																		You do not have to worry if it is hidden, the next <td> will automatically
																		center and apply to the width 100% and also shrink to 50% if the first <td>
																		is visible.
																	-->
																<td align="left" valign="middle" id="invisibleIntroduction" class="flexibleContainerBox" style="display:none !important; mso-hide:all;">
																	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:100%;">
																		<tr>
																			<td align="left" class="textContent">
																				<div style="font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:13px;color:#828282;text-align:center;line-height:120%;">
																					CekOri Customer Report																				
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
																<!-- <td align="right" valign="middle" class="flexibleContainerBox">
																	<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:100%;">
																		<tr>
																			<td align="left" class="textContent">
																				<div style="font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:13px;color:#828282;text-align:center;line-height:120%;">
																					If you can't see this message, <a href="#" target="_blank" style="text-decoration:none;color:#91191b;"><span style="color:#91191b;">view&nbsp;it&nbsp;in&nbsp;your&nbsp;browser</span></a>.
																				</div>
																			</td>
																		</tr>
																	</table>
																</td> -->
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<!-- // FLEXIBLE CONTAINER -->
										</td>
									</tr>
								</table>
								<!-- // CENTERING TABLE -->
							</td>
						</tr>
						<!-- // END -->

					</table>
					<!-- // END -->

					<!-- EMAIL BODY // -->
					<!--
							The table "emailBody" is the email's container.
							Its width can be set to 100% for a color band
							that spans the width of the page.
						-->
					<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0" width="500" id="emailBody">

						<!-- MODULE ROW // -->
						<!--
								To move or duplicate any of the design patterns
								in this email, simply move or copy the entire
								MODULE ROW section for each content block.
							-->
						<tr>
							<td align="center" valign="top">
								<!-- CENTERING TABLE // -->
								<!--
										The centering table keeps the content
										tables centered in the emailBody table,
										in case its width is set to 100%.
									-->
								<table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#FFFFFF;background:#91191b">
									<tr>
										<td align="center" valign="top">
											<!-- FLEXIBLE CONTAINER // -->
											<!--
													The flexible container has a set width
													that gets overridden by the media query.
													Most content tables within can then be
													given 100% widths.
												-->
											<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
												<tr>
													<td align="center" valign="top" width="500" class="flexibleContainerCell">

														<!-- CONTENT TABLE // -->
														<!--
															The content table is the first element
																that's entirely separate from the structural
																framework of the email.
															-->
														<table border="0" cellpadding="30" cellspacing="0" width="100%">
															<tr>
																<td align="center" valign="top" class="textContent">
																	<img src="{{ asset('app-logo.png') }}" alt="CekOri Logo" title="CekOri Logo" style="width:200px;height:auto;margin-bottom:20px;">
																	<h2 style="text-align:center;font-weight:normal;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:23px;margin-bottom:10px;color:#FFFFFF;line-height:135%;">CekOri Application Update Notifiation</h2>
																</td>
															</tr>
														</table>
														<!-- // CONTENT TABLE -->

													</td>
												</tr>
											</table>
											<!-- // FLEXIBLE CONTAINER -->
										</td>
									</tr>
								</table>
								<!-- // CENTERING TABLE -->
							</td>
						</tr>
						<!-- // MODULE ROW -->


						<!-- MODULE ROW // -->
						<!--  The "mc:hideable" is a feature for MailChimp which allows
								you to disable certain row. It works perfectly for our row structure.
								http://kb.mailchimp.com/article/template-language-creating-editable-content-areas/
							-->
						<!-- MODULE ROW // -->
						<tr>
							<td align="center" valign="top">
								<!-- CENTERING TABLE // -->
								<table border="0" cellpadding="10" cellspacing="0" width="100%" bgcolor="#91191b">
									<tr>
										<td align="center" valign="top">
											<!-- FLEXIBLE CONTAINER // -->
											<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer" bgcolor="#fff" style="border-radius:5px;margin-top:-15px;">
												<tr>
													<td align="center" valign="top" width="500" class="flexibleContainerCell">
														<table border="0" cellpadding="30" cellspacing="0" width="100%">
															<tr>
																<td align="center" valign="top">

																	<!-- CONTENT TABLE // -->
																	<table border="0" cellpadding="0" cellspacing="0" width="100%">
																		<tr>
																			<td valign="top" class="textContent">
																				<!--
																						The "mc:edit" is a feature for MailChimp which allows
																						you to edit certain row. It makes it easy for you to quickly edit row sections.
																						http://kb.mailchimp.com/templates/code/create-editable-content-areas-with-mailchimps-template-language
																					-->

																				<h3 mc:edit="header" style="color:#91191b;line-height:200%;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Dear CekOri Customer,</h3>
																				
																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">It has been 7 months since our last update in May 2020. This is not because of the pandemic since we have been hard at work to revamp the CekOri app fundamentally from the ground up to leap frog other competitions.</div>
																				<br/>

																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">We have done a fundamental change of CekOri authenticity philosophy, from a simple authenticating a QR to determine the authenticity of a product, we now protect an end to end process since product manufacturing stage till the end of the product packaging in our brand manufacturer side. We now use 2 separate QR codes to form a robust 2 Factor Authentication with Quasi Blockchain technology.</div>
																				<br/>
																				
																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">1. The first QR scan on the packaging of the product will now pop up bonafide product information and attributes coming directly from the brand manufacturer. So you know the product is registered and certified before you even decide to buy the product.</div>
																				<br/>

																				<div mc:edit="body">
																					<table style="margin-left:auto;margin-right:auto;">
																						<tr>
																							<td style="padding: 0; margin: 0; border: 0.5px solid black;">
																								<div style="width: 170px;height: 130px;display:flex;">
																									<div>
																										<img src="{{ asset('border_zeta.jpeg') }}" style="width:50px;height:130px">
																									</div>
																									<div style="text-align:center;padding-left:10px;padding-top: 1px;">
																										<figcaption style="font-size:6px;color:#05747e">Brand Manufacturer</figcaption>
																										<div style="margin: 1px 0px;width:100px;height:100px;">
																											<img src="{!! $message->embedData(QrCode::format('png')->color(5, 117, 127)->generate('ZETA'), 'QrCode.png', 'image/png')!!}" alt="CekOri Logo" title="CekOri Logo" style="width:100px;height:100px;background-color: #4a1c40;" >
																										</div>
																										<figcaption style="font-size:6px;color:#05747e;margin-top:-4px">ZETA</figcaption>
																									</div>
																								</div>
																							</td>
																						</tr>
																						<tr>
																							<td>
																								<br />
																							</td>
																						</tr>
																					</table>
																				</div>
																				<br />

																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">2. Once you purchased the product and open the packaging you will find a more protected 2nd QR which after being paired with the 1st QR will pop up an authenticity report whether you are the first one to pair these QRs and therefore ascertained whether the product was tampered or exchanged before your purchase.</div>
																				<br/>

																				<div mc:edit="body">
																					<table style="margin-left:auto;margin-right:auto;">
																						<tr>
																							<td style="padding: 0; margin: 0; border: 0.5px solid black;">
																								<div style="width: 170px;height: 130px;display:flex;">
																									<div>
																										<img src="{{ asset('border_alpha.jpeg') }}" style="width:50px;height:130px">
																									</div>
																									<div style="text-align:center;padding-left:10px;padding-top: 1px;">
																										<figcaption style="font-size:6px;color:#910004">Brand Manufacturer</figcaption>
																										<div style="margin: 1px 0px;width:100px;height:100px;">
																											<img src="{!! $message->embedData(QrCode::format('png')->color(145, 0, 4)->generate('ALPHA'), 'QrCode.png', 'image/png')!!}" alt="CekOri Logo" title="CekOri Logo" style="width:100px;height:100px;background-color: #4a1c40;" >
																										</div>
																										<figcaption style="font-size:6px;color:#910004;margin-top:-4px">ALPHA</figcaption>
																									</div>
																								</div>
																							</td>
																						</tr>
																						<tr>
																							<td>
																								<br />
																							</td>
																						</tr>
																					</table>
																				</div>
																				<br />
																				
																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">These end to end 2 Factor Authentication Quasi Blockchain is way better than any competing product in the market since it protects you with an end to end process.</div>
																				<br/>

																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">You now also have a transactional history record of all products you have paired and authenticated with CekOri and so give you a lot more than just a simple authentication as with the older version of CekOri. It is also more transparent in asking a privacy policy permission and explains why you need to register and give permission access to use your camera as well as your location when you are performing a QR scan.</div>
																				<br/>

																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">We believe you will like the new CekOri since we have been thrashing many bugs and instability in the older previous version of CekOri. Better still, this new CekOri app is also backward compatible with the previous QR used in prior products in the market although those older QR products will not show Product Information and will not be recorded in the transaction history.</div>
																				<br/>

																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">To use the CekOri new app, all you need to do is just click the following upgrade app button (depending on whether you are using iOS or Android version of CekOri). That is all there is for the iOS version users.</div>
																				<br/>

																				<div mc:edit="body" style="text-align:left;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;">For those of you on Android version, the upgrade button will upgrade the old CekOri app and present a new button from which you can click to install the new CekOri apps. Once the new CekOri apps version is installed you can just delete the old CekOri Android version forever and you can just proceed to login with your existing CekOri account you had, it will just work and you can start experiencing all the above benefits and improvements.</div>
																				<br/>

																				<table border="0" width="100%">
																					<tr>
																						<td valign="top" style="text-align:center;">
																							<a href="https://apps.apple.com/us/app/cekori/id1478556984"><img src="{{ asset('button_ios_store.png') }}" style="width:50%"></a>
																						</td>
																					</tr>
																				</table>
																				<br />

																				<table border="0" width="100%">
																					<tr>
																						<td style="text-align:center;font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:15px;margin-bottom:5px;color:#5F5F5F;line-height:150%;vertical-align: top;">Or</td>
																					</tr>
																				</table>
																				<br />

																				<table border="0" width="100%">
																					<tr>
																						<td valign="top" style="text-align:center;">
																							<a href="https://play.google.com/store/apps/details?id=com.cekori"><img src="{{ asset('button_android_store.png') }}" style="width:50%"></a>
																						</td>
																					</tr>
																				</table>
																				<br />

																			</td>
																		</tr>
																	</table>
																	<!-- // CONTENT TABLE -->
																</td>
															</tr>
														</table>
													</td>
												</tr>
											</table>
											<!-- // FLEXIBLE CONTAINER -->
										</td>
									</tr>
								</table>
								<!-- // CENTERING TABLE -->
							</td>
						</tr>

						<!-- MODULE ROW // -->
						<tr>
							<td align="center" valign="top">
								<!-- CENTERING TABLE // -->
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td align="center" valign="top">
											<!-- FLEXIBLE CONTAINER // -->
											<table border="0" cellpadding="0" cellspacing="0" width="100%" class="flexibleContainer">
												<tr>
													<td valign="top" width="500" class="flexibleContainerCell">

														<!-- CONTENT TABLE // -->
														<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%">
															<tr>
																<td align="center" valign="top" style="background-color:#91191b;width: 50%;">
																	<table border="0" cellpadding="30" cellspacing="0" width="100%" style="max-width:100%;">
																		<tr>
																		<tr>
																			<td align="center">
																					<div style="font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:24px;color:#fff;text-align:center;line-height:120%;font-weight: bold;margin-bottom:10px;">
																						<div>VISIT US</div>
																					</div>
																					<div style="font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:13px;color:#fff;text-align:center;line-height:120%;margin-bottom:10px;">
																						<div><a href="https://www.cekori.com/" target="_blank" style="text-decoration:none;color:#fff;"><span style="color:#fff;">www.cekori.com</span></a></div>
																					</div>
																					<div style="font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:13px;color:#fff;text-align:center;line-height:120%;margin-bottom:20px;">
																						<div><a href="https://www.instagram.com/cekori.id/" target="_blank" style="text-decoration:none;color:#fff;"><span style="color:#fff;">@cekori.id</span></a></div>
																					</div>
																					<table border="0" cellspacing="0" cellpadding="0" style="margin-left:15px;">
																						<tr>
																							<td class="img" width="55" style="font-size:0pt; line-height:0pt; text-align:right;"><a href="https://www.facebook.com/cekori.id" target="_blank"><img src="https://maelsov.id/cek_ori/assets/storages/email_images/ico_facebook.png" width="38" height="38" border="0" alt="" /></a></td>
																							<td class="img" width="55" style="font-size:0pt; line-height:0pt; text-align:left;"><a href="https://www.instagram.com/cekori.id/" target="_blank"><img src="https://maelsov.id/cek_ori/assets/storages/email_images/ico_instagram.png" width="38" height="38" border="0" alt="" /></a></td>
																						</tr>
																					</table>
																				</td>
																			</tr>
																			<td align="center" class="textContent">
																				<div style="font-family:'Avenir Next', Helvetica, Arial, sans-serif;;font-size:13px;color:#fff;text-align:center;line-height:120%;">
																					<div>Copyright &#169; {{ date('Y') }} <a href="http://134.209.124.184/" target="_blank" style="text-decoration:none;color:#fff;"><span style="color:#fff;">CekOri</span></a>. All&nbsp;rights&nbsp;reserved.</div>
																				</div>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
														</table>
														<!-- // CONTENT TABLE -->

													</td>
												</tr>
											</table>
											<!-- // FLEXIBLE CONTAINER -->
										</td>
									</tr>
								</table>
								<!-- // CENTERING TABLE -->
							</td>
						</tr>
						<!-- // MODULE ROW -->

					</table>
					<!-- // END -->

					<!-- EMAIL FOOTER // -->
					<!--
							The table "emailBody" is the email's container.
							Its width can be set to 100% for a color band
							that spans the width of the page.
						-->
					<!-- // END -->

				</td>
			</tr>
		</table>
	</center>
</body>

</html>