<template>
  <section class="chat-box">
    <div class="chat-box-list-container" ref="chatbox">
      <ul class="chat-box-list">
        <li
                class="message"
                v-for="(message, idx) in messages"
                :key="idx"
                :class="message.author"
        >
          <p>
            <span v-if="message.author === 'client'">{{ message.text }}</span>
            <span v-if="message.author === 'server'" v-html="message.text"></span>
          </p>

        </li>
      </ul>
    </div>
    <div class="chat-inputs">
      <input
              type="text"
              v-model="message"
              @keyup.enter="sendMessage (message, 'client')"
      />
      <button @click="sendMessage">Send</button>
    </div>
    <modal name="loginForm" width="400px" height="auto" :clickToClose="false">
      <div class="login-container">
        <b-card>
        <b-form-group
                id="login-group-1"
                label="Email address:"
                label-for="input-1"
                description="We'll never share your email with anyone else."
        >
          <b-form-input
                  id="login-input-1"
                  v-model="username"
                  type="email"
                  required
                  placeholder="Enter email"
          ></b-form-input>
        </b-form-group>
        <b-form-group id="login-group-2" label="Password:" label-for="input-2">
          <b-form-input
                  id="login-input-2"
                  v-model="password"
                  type="password"
                  required
                  placeholder="Enter password"
                  @keyup.enter="processLogin()"
          ></b-form-input>
        </b-form-group>
        <b-button  @click="processLogin()" variant="success">Login</b-button>
        </b-card>
      </div>
    </modal>
    <modal name="registerForm" width="400px" height="auto" :clickToClose="true" >
      <div class="form-container">
        <b-card>
        <h5 class="col-12">
          Register Form
        </h5>
        <b-form-group id="register-group-1" label="Your Name:" label-for="register-input-1">
          <b-form-input
                  id="register-input-1"
                  v-model="name"
                  required
                  placeholder="Enter name"
          ></b-form-input>
        </b-form-group>
        <b-form-group
                id="register-group-2"
                label="Email address:"
                label-for="register-input-2"
        >
          <b-form-input
                  id="register-input-2"
                  v-model="email"
                  type="email"
                  required
                  placeholder="Enter email"
          ></b-form-input>
        </b-form-group>
        <b-form-group id="register-group-3" label="Password:" label-for="register-input-3">
          <b-form-input
                  id="register-input-3"
                  type="password"
                  v-model="password"
                  required
                  placeholder="Type your Password"
          ></b-form-input>
        </b-form-group>
        <b-form-group id="register-group-4" label="Password Confirmation:" label-for="register-input-4">
          <b-form-input
                  id="register-input-4"
                  type="password"
                  v-model="passwordConfirm"
                  required
                  placeholder="Confirm your Password"
                  @keyup.enter="processRegister()"
          ></b-form-input>
        </b-form-group>
        <b-form-group id="register-group-5" label="Currency (opcional):" label-for="register-input-5">
          <b-form-input
                  id="register-input-5"
                  v-model="def_currency"
                  placeholder="Currency Code"
          ></b-form-input>
        </b-form-group>
        <b-form-group id="register-group-6" label="Initial Amount (opcional):" label-for="register-input-6">
          <b-form-input
                  id="register-input-6"
                  v-model="def_amount"
                  placeholder="Initial Amount"
          ></b-form-input>
        </b-form-group>
        <div v-if="formError" class="alert alert-danger col-12"><pre>{{ formError }}</pre></div>
        <div class="form-register-buttons">
          <b-button variant="success" @click="processRegister()">Register</b-button>
        </div>
        </b-card>
      </div>
    </modal>
  </section>
</template>

<script src="./ChatBox.js" />
<style lang="scss" src="./ChatBox.scss" />
