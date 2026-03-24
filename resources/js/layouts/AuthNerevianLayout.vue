<template>
  <div class="auth-wrapper">
    <!-- Contenedor centralizado para emular exactamente el diseño de las imágenes -->
    <div class="auth-container">
      <div class="brand-header">
        <div class="brand-logo-container">
          <img src="/logo.svg" alt="Nerevian" class="actual-logo" />
        </div>
      </div>
      <slot />
    </div>
  </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.auth-wrapper {
  display: flex;
  min-height: 100vh;
  margin: 0;
  padding: 0;
  background-color: #f7f3ef; /* Crema muy claro / gris cálido como en la imagen */
  align-items: center;
  justify-content: center;
  font-family: 'Inter', sans-serif;
}

.auth-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 450px;
  padding: 2rem 1rem;
}

.brand-header {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 2rem;
}

.actual-logo {
  max-width: 250px;
  height: auto;
  display: block;
}
</style>
