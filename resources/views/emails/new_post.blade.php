<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Publicación — EBT</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f9fc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td align="center" style="background-color: #f6f9fc; padding: 40px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="background-color: #212529; padding: 25px 20px;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px;">
                                <span style="color: #dc3545;">EBT</span> Servicios Profesionales
                            </h1>
                            <p style="color: #adb5bd; margin: 5px 0 0 0; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px;">Portal de Clientes</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 35px; color: #495057; line-height: 1.6;">
                            <h2 style="color: #212529; margin-top: 0; font-size: 18px; font-weight: 600;">
                                Estimado/a {{ $notifiable->name }},
                            </h2>
                            <p style="margin-bottom: 25px; font-size: 15px; color: #495057;">
                                Le informamos que se ha publicado nueva información y evidencias en su cuenta comercial de EBT.
                            </p>

                            <!-- Info Card -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8f9fa; border-radius: 6px; margin-bottom: 30px; border-left: 4px solid #0d6efd;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="30%" style="font-size: 11px; color: #6c757d; font-weight: bold; padding-bottom: 8px; letter-spacing: 0.5px; text-transform: uppercase; vertical-align: top;">Empresa</td>
                                                <td width="70%" style="font-size: 14px; color: #212529; padding-bottom: 8px; font-weight: 600;">{{ $company->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 11px; color: #6c757d; font-weight: bold; padding-bottom: 8px; letter-spacing: 0.5px; text-transform: uppercase; vertical-align: top;">Proyecto</td>
                                                <td style="font-size: 14px; color: #212529; padding-bottom: 8px; font-weight: 600;">{{ $project->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-size: 11px; color: #6c757d; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; vertical-align: top;">Publicación</td>
                                                <td style="font-size: 14px; color: #212529; font-weight: 600;">{{ $post->title }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <!-- Call to Action -->
                            <div style="margin-top: 35px; border-top: 1px dashed #dee2e6; padding-top: 25px; text-align: center;">
                                <p style="font-size: 13px; color: #6c757d; margin: 0 0 15px 0;">
                                    Para revisar esta publicación y descargar los archivos adjuntos, por favor acceda al Portal de Clientes desde su navegador habitual:
                                </p>
                                <a href="https://clientes.ebtserviciosprofesionales.com" target="_blank" style="display: inline-block; background-color: #0d6efd; color: #ffffff; padding: 12px 25px; border-radius: 5px; font-weight: 600; font-size: 14px; text-decoration: none; box-shadow: 0 2px 4px rgba(13,110,253,0.15);">
                                    Acceder al Portal
                                </a>
                                <p style="font-size: 11px; color: #adb5bd; margin: 8px 0 0 0;">
                                    clientes.ebtserviciosprofesionales.com
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f8f9fa; padding: 25px; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 11px; line-height: 1.5;">
                            <p style="margin: 0 0 5px 0; font-weight: bold; color: #495057;">EBT Servicios Profesionales</p>
                            <p style="margin: 0 0 10px 0;">Este es un mensaje automático de notificación del sistema. Por favor no responda a esta dirección de correo.</p>
                            <p style="margin: 0; color: #adb5bd;">&copy; {{ date('Y') }} EBT. Todos los derechos reservados.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
