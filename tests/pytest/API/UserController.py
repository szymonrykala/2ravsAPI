import requests


class User():
    rawUrl = "http://localhost:8080"
    url = "http://localhost:8080/users"

    def __init__(self):
        self.__auth = {"authorization": ''}

    @property
    def auth(self):
        return self.__auth

    @auth.setter
    def auth(self, token):
        self.__auth['authorization'] = f"Bearer {token}"

    def handleExt(self, url, extensions):
        url += "ext="
        if type(extensions) is not list:
            raise TypeError('Extensions have to be a list')

        for ext in extensions:
            url += f"ext,"

        return url

    def registerUser(self, data):
        """data = {
            "email": email,
            "name": name,
            "surname": surname,
            "password": password
        } """
        resp = requests.post(url=self.url, json=data)
        return resp.json()

    def verifyUser(self, email, password):
        data = {'email': email, 'password': password}
        resp = requests.post(url=f'{self.rawUrl}/auth', json=data)
        json = resp.json()
        return resp

    def activateUser(self):
        pass

    def resendActivationEmail(self):
        pass

    def logoutUser(self):
        pass

    def getAllUsers(self, extensions):
        url = self.handleExt(self.url, extensions)
        resp = requests.post(url=url, headers=self.auth)
        return resp.json()

    def getSpecificUser(self, id, extensions):
        url = self.handleExt(self.url+f'/{id}', extensions)
        resp = requests.post(url=url, headers=self.auth)
        return resp.json()

    def updateUserInformations(self, id, data):
        resp = requests.post(
            url=self.url+f"/{id}", json=data, headers=self.auth)
        return resp.json()

    def deleteUser(self, id):
        resp = requests.post(url=self.url+f"/{id}", headers=self.auth)
        return resp.json()


if __name__ == '__main__':
    user = User()
    
