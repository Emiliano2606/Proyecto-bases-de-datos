   document.addEventListener('DOMContentLoaded', () => {
       const cpInput = document.getElementById('codigo_postal');
       const estadoInput = document.getElementById('estado');
       const municipioInput = document.getElementById('municipio');
       const coloniaSelect = document.getElementById('colonia');
       const direccionFinal = document.getElementById('direccion_final');

       let cpData = [];

       // Cargar JSON
       fetch('cp.json')
           .then(response => response.json())
           .then(data => {
               cpData = data;
               console.log('Datos de CP cargados');
           })
           .catch(err => console.error('Error cargando JSON:', err));

       // Función para buscar CP
       function buscarCP(cp) {
           cp = cp.trim();
           if (cp.length !== 5 || isNaN(cp)) {
               alert('Ingresa un Código Postal válido de 5 dígitos');
               return;
           }

           const resultado = cpData.find(item => item.cp === cp);
           if (resultado) {
               estadoInput.value = resultado.estado;
               municipioInput.value = resultado.municipio;

               // Llenar select de colonias
               coloniaSelect.innerHTML = '<option value="">-- Seleccione Colonia --</option>';
               resultado.colonias.forEach(col => {
                   const opt = document.createElement('option');
                   opt.value = col;
                   opt.textContent = col;
                   coloniaSelect.appendChild(opt);
               });
               coloniaSelect.disabled = false;
           } else {
               estadoInput.value = '';
               municipioInput.value = '';
               coloniaSelect.innerHTML = '<option value="">-- Colonia no disponible --</option>';
               coloniaSelect.disabled = true;
           }
           actualizarDireccion();
       }

       // Función para mostrar la dirección final
       function actualizarDireccion() {
           const cp = cpInput.value;
           const estado = estadoInput.value;
           const municipio = municipioInput.value;
           const colonia = coloniaSelect.value;
           const calle = document.getElementById('calle_mascota').value;
           const numero = document.getElementById('numero_mascota').value;

           direccionFinal.textContent = `Dirección seleccionada: ${calle} ${numero}, Colonia: ${colonia}, ${municipio}, ${estado}, CP: ${cp}`;
       }

       // Event listeners
       cpInput.addEventListener('blur', () => buscarCP(cpInput.value));
       coloniaSelect.addEventListener('change', actualizarDireccion);
       document.getElementById('calle_mascota').addEventListener('input', actualizarDireccion);
       document.getElementById('numero_mascota').addEventListener('input', actualizarDireccion);
   });